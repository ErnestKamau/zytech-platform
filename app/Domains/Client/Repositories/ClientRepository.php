<?php

namespace App\Domains\Client\Repositories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class ClientRepository
{
    public function findByEmail(string $email): ?Client
    {
        $normalized = Str::lower(trim($email));

        if ($normalized === '') {
            return null;
        }

        $byClientEmail = Client::query()
            ->whereRaw('LOWER(email) = ?', [$normalized])
            ->first();

        if ($byClientEmail !== null) {
            return $byClientEmail;
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$normalized])
            ->first();

        if ($user !== null) {
            $linked = Client::query()->where('user_id', $user->id)->first()
                ?? $user->clientProfile;

            if ($linked !== null) {
                return $linked;
            }
        }

        return Client::query()
            ->whereHas('contacts', fn ($query) => $query->whereRaw('LOWER(email) = ?', [$normalized]))
            ->first();
    }

    /**
     * @return Collection<int, Client>
     */
    public function active(): Collection
    {
        return Client::query()
            ->with(['assignedSales', 'tags'])
            ->whereIn('status', ['prospect', 'active'])
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Client>
     */
    public function search(string $term, int $limit = 20): Collection
    {
        $needle = trim($term);

        if ($needle === '') {
            return collect();
        }

        return Client::query()
            ->where(function ($query) use ($needle): void {
                $query
                    ->where('name', 'ilike', "%{$needle}%")
                    ->orWhere('email', 'ilike', "%{$needle}%")
                    ->orWhere('phone', 'ilike', "%{$needle}%");
            })
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public function findWithRelations(string $id): ?Client
    {
        return Client::query()
            ->with([
                'contacts',
                'addresses',
                'documents',
                'notes.author',
                'timeline',
                'communications.logger',
                'preferences',
                'tags',
                'groups',
                'projects',
                'quotationRequests',
                'quotations',
                'assignedSales',
                'assignedProjectManager',
            ])
            ->find($id);
    }
}
