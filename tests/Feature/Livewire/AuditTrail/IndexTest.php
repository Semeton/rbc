<?php

namespace Tests\Feature\Livewire\AuditTrail;

use App\Livewire\AuditTrail\Index;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(Index::class)
            ->assertStatus(200);
    }
}
