<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Setting;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition()
    {
        $key = $this->faker->unique()->word();
        $value = $this->faker->randomElement([
            $this->faker->word(),
            json_encode(['example' => $this->faker->word()]),
        ]);

        return [
            'name' => ucfirst(str_replace('_', ' ', $key)),
            'key' => $key,
            'value' => $value,
            'type' => 'string',
            'autoload' => true,
        ];
    }
}
