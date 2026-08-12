<?php

namespace App\Domains\Portal\Policies;

use App\Core\Enums\DocumentVisibility;
use App\Core\Policies\BasePolicy;
use App\Models\ClientDocument;
use App\Models\User;

final class DocumentPolicy extends BasePolicy
{
    public function view(User $user, ClientDocument $document): bool
    {
        if ($user->can('clients.manage')) {
            return true;
        }

        return $document->client?->user_id === $user->id
            && $document->visibility === DocumentVisibility::Client;
    }

    public function download(User $user, ClientDocument $document): bool
    {
        return $this->view($user, $document);
    }
}
