<?php

namespace BaksDev\Support\Repository\AllMessagesByEvent;

use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Id\SupportUid;

interface AllMessagesByEventInterface
{
    public function forSupportEvent(SupportEvent|SupportEventUid|string $event): self;

    /** Метод возвращает все сообщения по id тикета */
    public function findAll(): array|false;
}
