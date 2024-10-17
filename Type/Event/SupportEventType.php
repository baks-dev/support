<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Event;

use BaksDev\Core\Type\UidType\UidType;

final class SupportEventType extends UidType
{
    public function getClassType(): string
    {
        return SupportEventUid::class;
    }

    public function getName(): string
    {
        return SupportEventUid::TYPE;
    }
}
