<?php

namespace BaksDev\Support\Entity\Event;

use BaksDev\Support\Type\Event\SupportEventUid;

interface SupportEventInterface
{
    public function getEvent(): ?SupportEventUid;
}
