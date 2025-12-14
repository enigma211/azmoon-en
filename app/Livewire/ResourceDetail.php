<?php

namespace App\Livewire;

use App\Models\ResourceItem;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ResourceDetail extends Component
{
    public ResourceItem $resource;

    public string $url;

    public function mount(int $resource): void
    {
        $this->resource = ResourceItem::findOrFail($resource);
        $this->url = $this->resolveUrl($this->resource->file_path);
    }

    protected function resolveUrl(?string $path): string
    {
        if (!$path) {
            return '';
        }
        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }
        // assume stored on public disk
        return Storage::url($path);
    }

    public function render()
    {
        return view('livewire.resource-detail')->layout('layouts.app');
    }
}
