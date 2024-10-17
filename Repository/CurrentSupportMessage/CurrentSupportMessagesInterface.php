<?php

namespace BaksDev\Support\Repository\CurrentSupportMessage;

use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Message\SupportMessage;
use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Message\SupportMessageUid;

interface CurrentSupportMessagesInterface
{
    public function execute(): SupportMessage|false;

    public function forMessage(SupportMessageUid|string $supportMessageUid): self;

    public function forEvent(SupportEvent|SupportEventUid|string $event): self;
}
