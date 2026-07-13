<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(rand(1, 3), true);
        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? null,
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->paragraphs(rand(1, 3), true),
            'price' => $this->faker->randomFloat(2, 100, 5000),
            'stock' => $this->faker->numberBetween(0, 50),
            'image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}