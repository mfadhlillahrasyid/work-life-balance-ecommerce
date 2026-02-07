<?php

if (!function_exists('paginate')) {
    function paginate(
        array $items,
        int $perPage = 10,
        int $currentPage = 1
    ): array {
        $totalItems = count($items);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $perPage;

        return [
            'data' => array_slice($items, $offset, $perPage),
            'meta' => [
                'current_page' => $currentPage,
                'per_page'     => $perPage,
                'total_items'  => $totalItems,
                'total_pages'  => $totalPages,
                'has_prev'     => $currentPage > 1,
                'has_next'     => $currentPage < $totalPages,
            ],
        ];
    }
}
