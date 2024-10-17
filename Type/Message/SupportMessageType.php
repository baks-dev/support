<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Message;

use BaksDev\Core\Type\UidType\UidType;

final class SupportMessageType extends UidType
{
    public function getClassType(): string
    {
        return SupportMessageUid::class;
    }

    public function getName(): string
    {
        return SupportMessageUid::TYPE;
    }
}
