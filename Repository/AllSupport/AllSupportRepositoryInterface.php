<?php

namespace BaksDev\Support\Repository\AllSupport;

use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;

interface AllSupportRepositoryInterface
{
    public function search(SearchDTO $search): self;

    /** Метод возвращает пагинатор Support */
    public function findPaginator(): PaginatorInterface;
}
