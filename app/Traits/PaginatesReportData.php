<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait PaginatesReportData
{
    /**
     * Convert a collection to a paginated result
     */
    protected function paginateCollection(Collection $data, int $perPage = 15): LengthAwarePaginator
    {
        $currentPage = $this->getPage();
        $offset = ($currentPage - 1) * $perPage;
        $items = $data->slice($offset, $perPage);
        
        return new LengthAwarePaginator(
            $items,
            $data->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );
    }
}
