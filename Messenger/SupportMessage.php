<?php

declare(strict_types=1);

namespace BaksDev\Support\Messenger;

use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Id\SupportUid;

final readonly class SupportMessage
{
    public function __construct(
        private SupportUid $id,
        private SupportEventUid $event,
        private ?SupportEventUid $last = null
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
