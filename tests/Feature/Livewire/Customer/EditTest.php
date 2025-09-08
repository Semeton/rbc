<?php

namespace Tests\Feature\Livewire\Customer;

use App\Livewire\Customer\Edit;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully()
    {
        $customer = Customer::factory()->create();

        Livewire::test(Edit::class, ['customer' => $customer])
            ->assertStatus(200);
    }
}
