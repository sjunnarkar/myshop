<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $total = $this->faker->randomFloat(2, 10, 1000);
        $tax = $total * 0.1;
        
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . $this->faker->unique()->numberBetween(1000, 9999),
            'status' => $this->faker->randomElement(['processing', 'delivered', 'cancelled', 'returned']),
            'total' => $total,
            'tax' => $tax,
            'subtotal' => $total - $tax,
            'shipping_address' => $this->faker->address,
            'billing_address' => $this->faker->address,
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'notes' => $this->faker->optional()->sentence,
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function processing()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'processing',
            ];
        });
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function delivered()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'delivered',
            ];
        });
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function returned()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'returned',
            ];
        });
    }
} 