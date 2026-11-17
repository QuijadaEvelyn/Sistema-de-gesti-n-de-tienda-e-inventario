<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MensajeContacto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MensajeContacto>
 */
class MensajeContactoFactory extends Factory
{
    protected $model = MensajeContacto::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'email' => fake()->safeEmail(),
            'telefono' => fake()->phoneNumber(),
            'sucursal_id' => null,
            'asunto' => fake()->sentence(4),
            'mensaje' => fake()->paragraph(3),
            'estatus' => 'pendiente',
            'ip' => fake()->ipv4(),
        ];
    }

    /**
     * Estado para mensaje leído.
     */
    public function leido(): static
    {
        return $this->state(fn (array $attributes) => [
            'estatus' => 'leido',
        ]);
    }

    /**
     * Estado para mensaje respondido.
     */
    public function respondido(): static
    {
        return $this->state(fn (array $attributes) => [
            'estatus' => 'respondido',
        ]);
    }
}
