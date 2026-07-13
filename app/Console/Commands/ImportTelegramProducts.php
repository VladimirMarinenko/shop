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

    public function handle()
    {
        $this->info('Токен: ' . $this->botToken);
        $this->info('Канал: ' . $this->channelId);

        $client = new Client();
        $url = "https://api.telegram.org/bot{$this->botToken}/getUpdates";

        $this->info("Запрос к $url");

        $response = $client->get($url, [
            'query' => [
                'offset' => -1,   // попробуйте -1, чтобы получить последнее сообщение
                'limit' => 10
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $this->info('Ответ: ' . print_r($data, true));

        if (!isset($data['ok']) || !$data['ok']) {
            $this->error('Ошибка получения обновлений');
            return;
        }

        $updates = $data['result'];
        $count = 0;

        foreach ($updates as $update) {
            if (!isset($update['channel_post'])) continue;

            $message = $update['channel_post'];
            $text = $message['text'] ?? '';
            $photo = $message['photo'] ?? null;

            $parsed = $this->parseProduct($text);

            if (!$parsed) {
                $this->warn('Не удалось распарсить сообщение: ' . substr($text, 0, 50) . '...');
                continue;
            }

            // Проверяем дубликат по названию и цене
            $exists = Product::where('name', $parsed['name'])
                ->where('price', $parsed['price'])
                ->exists();

            if ($exists) {
                $this->info("Товар '{$parsed['name']}' уже существует");
                continue;
            }

            // Получаем или создаём категорию
            $categoryId = $this->getOrCreateCategory($parsed['category'], null);

            // Если есть подкатегория, создаём её внутри категории
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
            $product->slug = Str::slug($parsed['name']);
            $product->stock = $parsed['quantity'] ?? 1;
            $product->category_id = $finalCategoryId;
            $product->description = "Состояние: {$parsed['condition']}\nКоличество: {$parsed['quantity']} шт.";

            // Если есть фото, скачиваем
            if ($photo) {
                $fileId = end($photo)['file_id'];
                $filePath = $this->downloadPhoto($fileId);
                if ($filePath) {
                    $product->image = $filePath;
                }
            }

            $product->save();
            $count++;
            $this->info("Импортирован товар: {$product->name} в категорию '{$parsed['category']}'" . (!empty($parsed['subcategory']) ? " / '{$parsed['subcategory']}'" : ''));
        }

        $this->info("Импортировано товаров: {$count}");
    }

    /**
     * Парсит сообщение и возвращает массив с данными товара.
     */
    protected function parseProduct($text)
    {
        $lines = array_map('trim', explode("\n", $text));
        $data = [];

        foreach ($lines as $line) {
            if (empty($line)) continue;

            // Название — первая непустая строка (без ключевого слова)
            if (!isset($data['name']) && !str_contains($line, 'Наша цена') && !str_contains($line, 'Состояние') && !str_contains($line, 'Количество') && !str_contains($line, 'Категория') && !str_contains($line, 'Подкатегория')) {
                $data['name'] = $line;
                continue;
            }

            // Цена
            if (preg_match('/Наша цена\s*(\d+)\s*₽/u', $line, $matches)) {
                $data['price'] = (float) $matches[1];
                continue;
            }

            // Состояние
            if (preg_match('/Состояние\s*(.+)/u', $line, $matches)) {
                $data['condition'] = trim($matches[1]);
                continue;
            }

            // Количество
            if (preg_match('/Количество\s*(\d+)\s*шт/u', $line, $matches)) {
                $data['quantity'] = (int) $matches[1];
                continue;
            }

            // Категория
            if (preg_match('/Категория\s*(.+)/u', $line, $matches)) {
                $data['category'] = trim($matches[1]);
                continue;
            }

            // Подкатегория
            if (preg_match('/Подкатегория\s*(.+)/u', $line, $matches)) {
                $data['subcategory'] = trim($matches[1]);
                continue;
            }
        }

        // Проверяем обязательные поля
        if (empty($data['name']) || empty($data['price']) || empty($data['category'])) {
            return null;
        }

        // Значения по умолчанию
        $data['condition'] = $data['condition'] ?? 'Новое';
        $data['quantity'] = $data['quantity'] ?? 1;
        $data['subcategory'] = $data['subcategory'] ?? null;

        return $data;
    }

    /**
     * Получает или создаёт категорию.
     * Если передан $parentId, создаёт подкатегорию.
     */
    protected function getOrCreateCategory($name, $parentId = null)
    {
        $name = trim($name);
        if (empty($name)) return null;

        // Ищем существующую
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
        $client = new Client();

        $fileUrl = "https://api.telegram.org/bot{$this->botToken}/getFile";
        $response = $client->get($fileUrl, ['query' => ['file_id' => $fileId]]);
        $data = json_decode($response->getBody(), true);

        if (!isset($data['ok']) || !$data['ok']) return null;

        $filePath = $data['result']['file_path'];
        $downloadUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";

        $content = $client->get($downloadUrl)->getBody()->getContents();

        $filename = 'products/' . uniqid() . '.jpg';
        $fullPath = storage_path('app/public/' . $filename);
        file_put_contents($fullPath, $content);

        return $filename;
    }
}
