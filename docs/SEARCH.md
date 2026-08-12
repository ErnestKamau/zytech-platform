# Global Search

> Phase 14 reference — PostgreSQL full-text search across the public catalogue.

## Purpose

`App\Domains\Search\Services\SearchService` is the single search orchestrator for website (and admin client lookup).

## Public UI

- Route: `/search`
- Livewire: `website.search-page`
- Header nav includes Search

## Behaviour

- PostgreSQL `to_tsvector` / `plainto_tsquery` with `ilike` fallback
- Indexes on projects, services, articles
- Redis caches result sets (5 minutes) and popular queries (1 hour)
- Every query is recorded in `search_queries` for suggestions/analytics

## Contexts

- `website` — published projects, services, articles
- `admin` — also clients when the user has `clients.view`
