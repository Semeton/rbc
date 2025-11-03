<?php

declare(strict_types=1);

namespace App\Livewire\ATC;

use App\Models\Atc;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Atc $atc;

    public function render(): View
    {
        return view('livewire.atc.show');
    }
}
