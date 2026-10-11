<?php

/**
 * KRA eTIMS integration configuration.
 *
 * Talks to the "injonge" VSCU middleware (the same proxy polucon uses).
 * There is no safe default base_url — it must be set explicitly per
 * environment via ETIMS_BASE_URL, otherwise the HTTP client refuses to
 * send anything rather than silently calling a stale/incorrect host.
 */

return [
    'base_url' => env('ETIMS_BASE_URL'),

    'urls' => [
        'add_items' => 'update_items',
        'sales' => env('ETIMS_SALES_ENDPOINT', 'injongeReceiptsEtimsVscuTest'),
    ],

    'credit_note_refno_prefix' => env('ETIMS_CREDIT_NOTE_REFNO_PREFIX', 'CN-'),
];
