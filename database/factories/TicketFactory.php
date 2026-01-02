<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
  protected $model = Ticket::class;

  public function definition(): array
  {
    $statuses = ['open', 'pending', 'solved', 'closed'];
    $priorities = ['low', 'normal', 'high', 'urgent'];

    $createdDatetime = $this->faker->dateTimeBetween('-30 days', 'now');
    $openedDatetime = $this->faker->dateTimeBetween($createdDatetime, 'now');

    return [
      'gorgias_ticket_id' => $this->faker->unique()->numberBetween(100000, 999999),
      'uri' => '/api/tickets/' . $this->faker->numberBetween(100000, 999999),
      'subject' => $this->faker->sentence(),
      'description' => $this->faker->paragraph(),
      'status' => $this->faker->randomElement($statuses),
      'priority' => $this->faker->randomElement($priorities),
      'requester_id' => $this->faker->numberBetween(100000, 999999),
      'requester_email' => $this->faker->safeEmail(),
      'requester_name' => $this->faker->name(),
      'requester_firstname' => $this->faker->firstName(),
      'requester_lastname' => $this->faker->lastName(),
      'assignee_user_id' => $this->faker->optional()->numberBetween(100000, 999999),
      'assignee_team_id' => $this->faker->optional()->numberBetween(1, 200),
      'local_user_id' => User::inRandomOrder()->first()?->id,
      'local_assigned_to' => $this->faker->optional()->passthrough(User::inRandomOrder()->first()?->id),
      'is_unread' => $this->faker->boolean(30),
      'created_datetime' => $createdDatetime,
      'opened_datetime' => $openedDatetime,
      'last_received_message_datetime' => $this->faker->dateTimeBetween($openedDatetime, 'now'),
      'last_message_datetime' => $this->faker->dateTimeBetween($openedDatetime, 'now'),
      'updated_datetime' => $this->faker->dateTimeBetween($openedDatetime, 'now'),
      'closed_datetime' => $this->faker->optional()->dateTimeBetween($openedDatetime, 'now'),
      'trashed_datetime' => null,
      'snooze_datetime' => null,
      'satisfaction_survey' => null,
      'custom_fields' => [
        'contact_reason' => $this->faker->randomElement([
          'Pre-sale::Product question',
          'Order::Cancel',
          'Order::Status',
          'Shipping::Delivery not received',
          'Return',
          'Warranty',
        ]),
        'product' => $this->faker->randomElement([
          'Accelera 351 Sport GD',
          'Accelera 651 Sport',
          'Accelera RA 162',
          'Multiple Tires',
        ]),
        'resolution' => $this->faker->optional()->randomElement([
          'No action',
          'Refund',
          'Information Given',
          'Order::Updated Information',
        ]),
      ],
    ];
  }

  public function open(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'open',
      'closed_datetime' => null,
    ]);
  }

  public function closed(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'closed',
      'closed_datetime' => now()->subDays(rand(1, 10)),
    ]);
  }

  public function unread(): static
  {
    return $this->state(fn(array $attributes) => [
      'is_unread' => true,
    ]);
  }

  public function highPriority(): static
  {
    return $this->state(fn(array $attributes) => [
      'priority' => 'high',
    ]);
  }
}
