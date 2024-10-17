<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Id;

use BaksDev\Core\Type\UidType\UidType;

final class SupportType extends UidType
{
    public function getClassType(): string
    {
        return SupportUid::class;
    }

    public function getName(): string
    {
        return SupportUid::TYPE;
    }
}
