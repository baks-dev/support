<?php

namespace BaksDev\Support\Repository\AllMessagesByEvent;

use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Type\Event\SupportEventUid;

interface AllMessagesByEventInterface
{
    public function forSupportEvent(SupportEvent|SupportEventUid|string $event): self;

    /** Метод возвращает все сообщения по id тикета */
    public function findAll(): array|false;
}
