<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ImportTelegramProducts extends Command
{
    protected $signature = 'telegram:import {limit=10}';
    protected $description = 'Импорт товаров из Telegram канала с категориями и подкатегориями';

    protected $botToken;
    protected $channelId;

    public function __construct()
    {
        parent::__construct();
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->channelId = env('TELEGRAM_CHANNEL_ID');
    }

    protected function getClient(): Client
    {
        $config = [
            'timeout' => 30,
            'connect_timeout' => 30,
        ];

        $proxyType = env('TELEGRAM_PROXY_TYPE');
        $proxyAddress = env('TELEGRAM_PROXY_ADDRESS');
        $proxyPort = env('TELEGRAM_PROXY_PORT');

        if ($proxyType && $proxyAddress && $proxyPort) {
            $proxy = $proxyType . '://' . $proxyAddress . ':' . $proxyPort;
            $username = env('TELEGRAM_PROXY_USERNAME');
            $password = env('TELEGRAM_PROXY_PASSWORD');
            if ($username && $password) {
                $proxy = $proxyType . '://' . $username . ':' . $password . '@' . $proxyAddress . ':' . $proxyPort;
            }
            $config['proxy'] = $proxy;
            $this->info("Используется прокси: {$proxy}");
        }

        return new Client($config);
    }

    public function handle()
    {
        if (!$this->botToken || !$this->channelId) {
            $this->error('TELEGRAM_BOT_TOKEN или TELEGRAM_CHANNEL_ID не указаны в .env');
            return;
        }

        $limit = $this->argument('limit');
        $client = $this->getClient();

        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/getUpdates";
            $response = $client->get($url, [
                'query' => [
                    'limit' => $limit
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            // ============ ОТЛАДКА ============
            $this->info('🔍 Ответ от Telegram:');
            $this->info('ok: ' . ($data['ok'] ? 'true' : 'false'));
            $this->info('Количество обновлений: ' . count($data['result'] ?? []));
            if (isset($data['result'][0])) {
                $this->info('Первое обновление:');
                dump($data['result'][0]);
            } else {
                $this->info('нет обновлений');
            }
            // =================================

            if (!isset($data['ok']) || !$data['ok']) {
                $this->error('Ошибка получения обновлений');
                return;
            }

            $updates = $data['result'];
            $count = 0;

            foreach ($updates as $update) {
                if (!isset($update['channel_post'])) {
                    $this->warn('⚠️ Пропущено: нет channel_post');
                    continue;
                }

                $message = $update['channel_post'];
                $text = $message['caption'] ?? '';
                if (empty($text)) {
                    $this->warn('⚠️ Сообщение без текста (только фото)');
                    continue;
                }
                $photo = $message['photo'] ?? null;

                $this->info('📝 Сообщение: ' . substr($text, 0, 100) . '...');

                $parsed = $this->parseProduct($text);

                if (!$parsed) {
                    $this->warn('❌ Не удалось распарсить сообщение:');
                    $this->warn('Полный текст: ' . $text);
                    continue;
                }

                $this->info('✅ Распарсено: ' . json_encode($parsed, JSON_UNESCAPED_UNICODE));

                // Проверяем дубликат по названию и цене
                $exists = Product::where('name', $parsed['name'])
                    ->where('price', $parsed['price'])
                    ->exists();

                if ($exists) {
                    $this->info("⏭️ Товар уже существует: {$parsed['name']}");
                    continue;
                }

                // Получаем или создаём категорию (если не указана, используем "Без категории")
                $categoryName = $parsed['category'] ?? 'Без категории';
                $categoryId = $this->getOrCreateCategory($categoryName, null);

                // Подкатегория (если есть)
                if (!empty($parsed['subcategory'])) {
                    $subCategoryId = $this->getOrCreateCategory($parsed['subcategory'], $categoryId);
                    $finalCategoryId = $subCategoryId;
                } else {
                    $finalCategoryId = $categoryId;
                }

                // Создаём товар
                $product = new Product();
                $product->name = $parsed['name'];
                $product->price = $parsed['price'];
                $product->slug = Str::slug($parsed['name'] . '-' . uniqid());
                $product->stock = $parsed['quantity'] ?? 1;
                $product->category_id = $finalCategoryId;
                $product->description = "Состояние: {$parsed['condition']}\nКоличество: {$parsed['quantity']} шт.";

                $this->info('📦 Попытка сохранить товар: ' . $product->name);
                $this->info('   slug: ' . $product->slug);
                $this->info('   price: ' . $product->price);
                $this->info('   category_id: ' . $product->category_id);

                // Фото
                if ($photo) {
                    $this->info('📸 Есть фото, скачиваем...');
                    $fileId = end($photo)['file_id'];
                    $filePath = $this->downloadPhoto($fileId);
                    if ($filePath) {
                        $product->image = $filePath;
                        $this->info('   фото сохранено: ' . $filePath);
                    } else {
                        $this->warn('   не удалось скачать фото');
                    }
                } else {
                    $this->info('📸 Фото отсутствует');
                }

                try {
                    $product->save();
                    $count++;
                    $this->info("✅ Импортирован товар: {$product->name} (ID: {$product->id})");
                } catch (\Exception $e) {
                    $this->error("❌ Ошибка при сохранении товара: " . $e->getMessage());
                    $this->error("   Трассировка: " . $e->getTraceAsString());
                }
            } // ← закрывающая скобка foreach

            $this->info("🎉 Импортировано товаров: {$count}");

        } catch (\Exception $e) {
            $this->error('❌ Ошибка: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }

    /**
     * Парсит сообщение и возвращает массив с данными товара.
     * Адаптирован под формат:
     *   Название
     *   Размер (опционально)
     *   Наша цена XXX₽
     *   Новое
     *   Писать: ...
     */
    protected function parseProduct($text)
    {
        $lines = array_map('trim', explode("\n", $text));
        $data = [
            'name' => null,
            'price' => null,
            'condition' => 'Новое',
            'quantity' => 1,
            'category' => 'Без категории',
            'subcategory' => null,
            'size' => null,
        ];

        foreach ($lines as $line) {
            if (empty($line)) continue;

            if (preg_match('/Наша цена\s*(\d+)\s*(?:₽|р)/u', $line, $matches)) {
                $data['price'] = (float) $matches[1];
                continue;
            }

            if ($line === 'Новое') {
                $data['condition'] = 'Новое';
                continue;
            }

            if (str_contains($line, 'Писать:')) {
                break;
            }

            if (str_starts_with($line, '@')) {
                continue;
            }

            if (str_contains($line, 'Размер')) {
                $data['size'] = $line;
                continue;
            }

            if (!$data['name'] && !str_contains($line, 'Наша цена') && !str_contains($line, 'Писать:')) {
                $data['name'] = $line;
            }
        }

        if (empty($data['name']) || empty($data['price'])) {
            return null;
        }

        if (empty($data['name']) && !empty($data['price'])) {
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                if (!str_contains($line, 'Наша цена') && !str_contains($line, 'Писать:') && !str_starts_with($line, '@')) {
                    $data['name'] = $line;
                    break;
                }
            }
        }

        return $data;
    }

    /**
     * Получает или создаёт категорию.
     */
    protected function getOrCreateCategory($name, $parentId = null)
    {
        $name = trim($name);
        if (empty($name)) return null;

        $query = Category::where('name', $name);
        if ($parentId !== null) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        $category = $query->first();

        if (!$category) {
            $category = Category::create([
                'name' => $name,
                'parent_id' => $parentId,
            ]);
            $this->info("Создана категория: {$name}" . ($parentId ? " (подкатегория)" : ""));
        }

        return $category->id;
    }

    /**
     * Скачивает фото из Telegram и сохраняет в storage.
     */
    protected function downloadPhoto($fileId)
    {
        $client = $this->getClient(); // теперь с прокси

        $fileUrl = "https://api.telegram.org/bot{$this->botToken}/getFile";
        $response = $client->get($fileUrl, ['query' => ['file_id' => $fileId]]);
        $data = json_decode($response->getBody(), true);

        if (!isset($data['ok']) || !$data['ok']) {
            return null;
        }

        $filePath = $data['result']['file_path'];
        $downloadUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";

        $content = $client->get($downloadUrl)->getBody()->getContents();

        $filename = 'products/' . uniqid() . '.jpg';
        $fullPath = storage_path('app/public/' . $filename);
        file_put_contents($fullPath, $content);

        return $filename;
    }
}
