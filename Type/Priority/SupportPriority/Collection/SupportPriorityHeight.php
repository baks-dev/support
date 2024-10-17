<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Priority\SupportPriority\Collection;

use BaksDev\Support\Type\Priority\SupportPriority\SupportPriorityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.support.priority')]
final class SupportPriorityHeight implements SupportPriorityInterface
{
    public const string PARAM = 'height';

    public function getValue(): string
    {
        return self::PARAM;
    }

    public static function priority(): int
    {
        return 10;
    }

    /** Проверяет, относится ли значение к данному объекту */
    public static function equals(string $param): bool
    {
        return self::PARAM === $param;
    }

}