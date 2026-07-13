<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Создаём 10 корневых категорий
        $rootCategories = [];
        for ($i = 1; $i <= 10; $i++) {
            $rootCategories[] = Category::create([
                'name' => 'Категория ' . $i,
            ]);
        }

        // Для каждой корневой категории создаём 4 подкатегории
        $subcategories = [];
        foreach ($rootCategories as $root) {
            for ($j = 1; $j <= 4; $j++) {
                $subcategories[] = Category::create([
                    'name' => $root->name . ' → Подкатегория ' . $j,
                    'parent_id' => $root->id,
                ]);
            }
        }

        // Все категории (корневые + подкатегории)
        $allCategories = array_merge($rootCategories, $subcategories);

        // Создаём 100 товаров
        $faker = \Faker\Factory::create('ru_RU');
        for ($i = 1; $i <= 100; $i++) {
            $category = $allCategories[array_rand($allCategories)];
            $name = $faker->unique()->words(rand(1, 3), true);
            Product::create([
                'category_id' => $category->id,
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name) . '-' . $i,
                'description' => $faker->paragraphs(rand(1, 3), true),
                'price' => $faker->randomFloat(2, 100, 5000),
                'stock' => $faker->numberBetween(0, 50),
                'image' => null,
            ]);
        }
    }
}
