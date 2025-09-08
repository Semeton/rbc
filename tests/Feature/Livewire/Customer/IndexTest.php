<?php

namespace Tests\Feature\Livewire\Customer;

use App\Livewire\Customer\Index;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully()
    {
        Livewire::test(Index::class)
            ->assertStatus(200);
    }
}
