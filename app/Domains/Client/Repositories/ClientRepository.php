<?php

namespace App\Domains\Client\Repositories;

use App\Models\Client;
use Illuminate\Support\Collection;

final class ClientRepository
{
    public function findByEmail(string $email): ?Client
    {
        return Client::query()->where('email', $email)->first();
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
