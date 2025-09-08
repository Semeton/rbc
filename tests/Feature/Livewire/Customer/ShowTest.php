<?php

namespace Tests\Feature\Livewire\Customer;

use App\Livewire\Customer\Show;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully()
    {
        $customer = Customer::factory()->create();

        Livewire::test(Show::class, ['customer' => $customer])
            ->assertStatus(200)
            ->assertSee($customer->name)
            ->assertSee($customer->email);
    }
}
