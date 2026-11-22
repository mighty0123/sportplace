<?php

namespace App\Livewire\Mitra;

use App\Livewire\Mitra\Concerns\WithMitraAuthorization;
use Livewire\Component;

abstract class MitraComponent extends Component
{
    use WithMitraAuthorization;
}
