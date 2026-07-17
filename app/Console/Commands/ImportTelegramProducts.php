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

                // Получаем или создаём категорию
                $categoryId = $this->getOrCreateCategory($parsed['category'], null);

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
            }

            $this->info("🎉 Импортировано товаров: {$count}");

        } catch (\Exception $e) {
            $this->error('❌ Ошибка: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }

    /**
     * Парсит сообщение и возвращает массив с данными товара.
     * Поддерживает оба формата: с префиксами (Название:, Наша цена:) и без.
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

            $lower = mb_strtolower($line);

            // --- ЦЕНА: ищем "Наша цена" или "цена" + число (без обязательной валюты) ---
            if (str_contains($lower, 'наша цена') || str_contains($lower, 'цена:')) {
                if (preg_match('/(\d+)/u', $line, $matches)) {
                    $data['price'] = (float) $matches[1];
                }
                continue;
            }

            // --- СОСТОЯНИЕ ---
            if (str_contains($lower, 'состояние')) {
                if (preg_match('/состояние\s*:?\s*(.+)/ui', $line, $matches)) {
                    $data['condition'] = trim($matches[1]);
                }
                continue;
            }

            // --- КОЛИЧЕСТВО: ищем "количество", "колличество", "кол-во" + число (без обязательного "шт") ---
            if (str_contains($lower, 'количество') || str_contains($lower, 'колличество') || str_contains($lower, 'кол-во')) {
                if (preg_match('/(\d+)/u', $line, $matches)) {
                    $data['quantity'] = (int) $matches[1];
                }
                continue;
            }

            // --- КАТЕГОРИЯ (исключаем подкатегории) ---
            if (str_contains($lower, 'категория') && !str_contains($lower, 'подкатегория') && !str_contains($lower, 'под категория')) {
                if (preg_match('/категория\s*:?\s*(.+)/ui', $line, $matches)) {
                    $data['category'] = trim($matches[1]);
                }
                continue;
            }

            // --- ПОДКАТЕГОРИЯ ---
            if (str_contains($lower, 'подкатегория') || str_contains($lower, 'под категория')) {
                if (preg_match('/под\s*категория\s*:?\s*(.+)/ui', $line, $matches)) {
                    $data['subcategory'] = trim($matches[1]);
                }
                continue;
            }

            // --- РАЗМЕР ---
            if (str_contains($lower, 'размер')) {
                if (preg_match('/размер\s*:?\s*(.+)/ui', $line, $matches)) {
                    $data['size'] = trim($matches[1]);
                }
                continue;
            }

            // --- ОСТАНОВКА: "Писать:" ---
            if (str_contains($lower, 'писать:')) {
                break;
            }

            // --- КОНТАКТЫ (@...) ---
            if (str_starts_with($line, '@')) {
                continue;
            }

            // --- НАЗВАНИЕ ---
            if (!$data['name']) {
                if (str_contains($lower, 'название:')) {
                    if (preg_match('/название\s*:?\s*(.+)/ui', $line, $matches)) {
                        $data['name'] = trim($matches[1]);
                    }
                } else {
                    $keywords = ['наша цена', 'цена', 'состояние', 'количество', 'колличество', 'кол-во', 'категория', 'подкатегория', 'под категория', 'размер', 'писать:'];
                    $isKeyword = false;
                    foreach ($keywords as $kw) {
                        if (str_contains($lower, $kw)) {
                            $isKeyword = true;
                            break;
                        }
                    }
                    if (!$isKeyword && !str_starts_with($line, '@')) {
                        $data['name'] = $line;
                    }
                }
            }
        }

        // Если цена не найдена, пробуем найти любое число в тексте (как последний шанс)
        if (empty($data['price'])) {
            if (preg_match('/(\d+)/u', $text, $matches)) {
                // Проверяем, что это не количество (если количество уже найдено и равно этому числу — пропускаем)
                if ($data['quantity'] != (int)$matches[1]) {
                    $data['price'] = (float) $matches[1];
                }
            }
        }

        // Если название не найдено, но цена есть — берём первую неключевую строку
        if (empty($data['name']) && !empty($data['price'])) {
            foreach ($lines as $line) {
                if (empty($line)) continue;
                $lower = mb_strtolower($line);
                $keywords = ['наша цена', 'цена', 'состояние', 'количество', 'колличество', 'кол-во', 'категория', 'подкатегория', 'под категория', 'размер', 'писать:'];
                $isKeyword = false;
                foreach ($keywords as $kw) {
                    if (str_contains($lower, $kw)) {
                        $isKeyword = true;
                        break;
                    }
                }
                if (!$isKeyword && !str_starts_with($line, '@')) {
                    $data['name'] = $line;
                    break;
                }
            }
        }

        if (empty($data['name']) || empty($data['price'])) {
            return null;
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
        $client = $this->getClient();

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
