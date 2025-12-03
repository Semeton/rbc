<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\TruckMovement;

use App\Livewire\TruckMovement\Create;
use App\Models\Atc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateFinancialCalculationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_atc_cost_and_fare_update_when_atc_selected(): void
    {
        $atc = Atc::factory()->create([
            'amount' => 5_000.00,
        ]);

        Livewire::test(Create::class)
            ->set('customer_cost', 15_000.00)
            ->set('atc_id', $atc->id)
            ->assertSet('atc_cost', 5_000.00)
            ->assertSet('fare', 10_000.00);
    }

    public function test_total_updates_when_gas_or_haulage_change(): void
    {
        $atc = Atc::factory()->create([
            'amount' => 8_000.00,
        ]);

        Livewire::test(Create::class)
            ->set('customer_cost', 20_000.00)
            ->set('atc_id', $atc->id) // fare should be 12,000
            ->set('gas_chop_money', 4_000.00)
            ->set('haulage', 1_500.00)
            ->assertSet('total_amount', 9_500.00);
    }

    public function test_total_plus_incentive_updates_when_incentive_changes(): void
    {
        $atc = Atc::factory()->create([
            'amount' => 6_500.00,
        ]);

        Livewire::test(Create::class)
            ->set('customer_cost', 18_000.00)
            ->set('atc_id', $atc->id) // fare 11,500
            ->set('gas_chop_money', 3_000.00)
            ->set('haulage', 500.00)
            ->set('incentive', 2_000.00)
            ->assertSet('total_amount', 9_000.00)
            ->assertSet('total_plus_incentive', 11_000.00);
    }

    public function test_atc_cost_updates_dynamically_when_atc_selected(): void
    {
        $atc1 = Atc::factory()->create(['amount' => 3_000.00]);
        $atc2 = Atc::factory()->create(['amount' => 7_500.00]);

        $component = Livewire::test(Create::class)
            ->assertSet('atc_cost', 0.0)
            ->set('atc_id', $atc1->id)
            ->assertSet('atc_cost', 3_000.00)
            ->set('atc_id', $atc2->id)
            ->assertSet('atc_cost', 7_500.00);
    }

    public function test_fare_updates_dynamically_when_customer_cost_changes(): void
    {
        $atc = Atc::factory()->create(['amount' => 5_000.00]);

        Livewire::test(Create::class)
            ->set('atc_id', $atc->id)
            ->set('customer_cost', 10_000.00)
            ->assertSet('fare', 5_000.00)
            ->set('customer_cost', 15_000.00)
            ->assertSet('fare', 10_000.00)
            ->set('customer_cost', 3_000.00) // Less than ATC cost
            ->assertSet('fare', 0.0); // Should never be negative
    }

    public function test_fare_updates_dynamically_when_atc_changes(): void
    {
        $atc1 = Atc::factory()->create(['amount' => 4_000.00]);
        $atc2 = Atc::factory()->create(['amount' => 8_000.00]);

        Livewire::test(Create::class)
            ->set('customer_cost', 20_000.00)
            ->set('atc_id', $atc1->id)
            ->assertSet('fare', 16_000.00)
            ->set('atc_id', $atc2->id)
            ->assertSet('fare', 12_000.00);
    }

    public function test_total_updates_dynamically_when_gas_changes(): void
    {
        $atc = Atc::factory()->create(['amount' => 5_000.00]);

        Livewire::test(Create::class)
            ->set('customer_cost', 20_000.00)
            ->set('atc_id', $atc->id) // fare = 15,000
            ->set('haulage', 1_000.00)
            ->set('gas_chop_money', 2_000.00)
            ->assertSet('total_amount', 14_000.00) // 15,000 - 2,000 + 1,000
            ->set('gas_chop_money', 5_000.00)
            ->assertSet('total_amount', 11_000.00); // 15,000 - 5,000 + 1,000
    }

    public function test_total_updates_dynamically_when_haulage_changes(): void
    {
        $atc = Atc::factory()->create(['amount' => 3_000.00]);

        Livewire::test(Create::class)
            ->set('customer_cost', 15_000.00)
            ->set('atc_id', $atc->id) // fare = 12,000
            ->set('gas_chop_money', 4_000.00)
            ->set('haulage', 500.00)
            ->assertSet('total_amount', 8_500.00) // 12,000 - 4,000 + 500
            ->set('haulage', -1_000.00) // Negative haulage
            ->assertSet('total_amount', 7_000.00) // 12,000 - 4,000 - 1,000
            ->set('haulage', 2_000.00)
            ->assertSet('total_amount', 10_000.00); // 12,000 - 4,000 + 2,000
    }

    public function test_total_plus_incentive_updates_dynamically_when_incentive_changes(): void
    {
        $atc = Atc::factory()->create(['amount' => 5_000.00]);

        Livewire::test(Create::class)
            ->set('customer_cost', 20_000.00)
            ->set('atc_id', $atc->id) // fare = 15,000
            ->set('gas_chop_money', 3_000.00)
            ->set('haulage', 1_000.00)
            ->set('incentive', 0.0)
            ->assertSet('total_amount', 13_000.00) // 15,000 - 3,000 + 1,000
            ->assertSet('total_plus_incentive', 13_000.00) // 13,000 + 0
            ->set('incentive', 2_500.00)
            ->assertSet('total_plus_incentive', 15_500.00) // 13,000 + 2,500
            ->set('incentive', 5_000.00)
            ->assertSet('total_plus_incentive', 18_000.00); // 13,000 + 5,000
    }

    public function test_all_fields_update_correctly_in_sequence(): void
    {
        $atc = Atc::factory()->create(['amount' => 6_000.00]);

        Livewire::test(Create::class)
            // Step 1: Set ATC and Customer Cost
            ->set('atc_id', $atc->id)
            ->assertSet('atc_cost', 6_000.00)
            ->set('customer_cost', 25_000.00)
            ->assertSet('fare', 19_000.00) // 25,000 - 6,000
            // Step 2: Set Gas and Haulage
            ->set('gas_chop_money', 5_000.00)
            ->set('haulage', 2_000.00)
            ->assertSet('total_amount', 16_000.00) // 19,000 - 5,000 + 2,000
            // Step 3: Set Incentive
            ->set('incentive', 3_000.00)
            ->assertSet('total_plus_incentive', 19_000.00) // 16,000 + 3,000
            // Step 4: Change Customer Cost (should update Fare, Total, Total + Incentive)
            ->set('customer_cost', 30_000.00)
            ->assertSet('fare', 24_000.00) // 30,000 - 6,000
            ->assertSet('total_amount', 21_000.00) // 24,000 - 5,000 + 2,000
            ->assertSet('total_plus_incentive', 24_000.00); // 21,000 + 3,000
    }

    public function test_fare_never_goes_negative_when_atc_cost_exceeds_customer_cost(): void
    {
        $atc = Atc::factory()->create(['amount' => 20_000.00]);

        Livewire::test(Create::class)
            ->set('atc_id', $atc->id)
            ->set('customer_cost', 15_000.00) // Less than ATC cost
            ->assertSet('atc_cost', 20_000.00)
            ->assertSet('fare', 0.0) // Should be 0, not negative
            ->set('customer_cost', 20_000.00) // Equal to ATC cost
            ->assertSet('fare', 0.0)
            ->set('customer_cost', 25_000.00) // Greater than ATC cost
            ->assertSet('fare', 5_000.00);
    }
}


