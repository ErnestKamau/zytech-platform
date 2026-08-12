<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use Illuminate\Contracts\View\View;

final class ContactForm extends BaseComponent
{
    public string $name = '';

    public string $email = '';

    public string $message = '';

    public bool $sent = false;

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $this->sent = true;
        $this->reset('name', 'email', 'message');
    }

    public function render(): View
    {
        return view('livewire.website.contact-form');
    }
}
