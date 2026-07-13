<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(rand(1, 2), true);
        return [
            'name' => $name,
            'parent_id' => null, 
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
