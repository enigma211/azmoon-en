<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ResourceItem;

class ResourcesPage extends Component
{
    public function render()
    {
        $items = ResourceItem::latest()->paginate(12);
        return view('livewire.resources-page', [
            'items' => $items,
        ])
            ->layout('layouts.app');
    }
}
