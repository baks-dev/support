<?php

declare(strict_types=1);

namespace BaksDev\Support\Messenger;

use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Id\SupportUid;

final class SupportMessage
{
    public function __construct(
        private readonly SupportUid $id,
        private readonly SupportEventUid $event,
        private readonly ?SupportEventUid $last = null
    ) {}

    /**
     * Идентификатор
     */
    public function getId(): SupportUid
    {
        return $this->id;
    }


    /**
     * Идентификатор события
     */
    public function getEvent(): SupportEventUid
    {
        return $this->event;
    }

    /**
     * Идентификатор предыдущего события
     */
    public function getLast(): ?SupportEventUid
    {
        return $this->last;
    }

}
