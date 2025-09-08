<?php

namespace Tests\Feature\Livewire\Customer;

use App\Livewire\Customer\Create;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully()
    {
        Livewire::test(Create::class)
            ->assertStatus(200);
    }
}
