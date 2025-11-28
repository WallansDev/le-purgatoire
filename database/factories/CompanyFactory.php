<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'siret' => $this->faker->numerify('##############'),
            'logo_path' => null,
            'address_line1' => $this->faker->streetAddress(),
            'address_line2' => null,
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'country' => 'France',
            'contact_name' => $this->faker->name(),
            'contact_email' => $this->faker->companyEmail(),
            'contact_phone' => $this->faker->e164PhoneNumber(),
        ];
    }
}
