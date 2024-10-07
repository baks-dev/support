<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Priority\SupportPriority\Collection;

use BaksDev\Support\Type\Priority\SupportPriority\SupportPriorityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.support.priority')]
final class SupportPriorityHeight implements SupportPriorityInterface
{
    public const PARAM = 'height';


    public function getValue(): string
    {
        return self::PARAM;
    }


    /**  Возвращает значение по умолчанию */
    public function default(): string|bool
    {
        return false;
    }

    /** Метод указывает, нужно ли добавить свойство для заполнения в форму */
    public function isSetting(): bool
    {
        return false;
    }


    public function required(): bool
    {
        return false;
    }

    public static function priority(): int
    {
        return 100;
    }

    /** Проверяет, относится ли значение к данному объекту */
    public static function equals(string $param): bool
    {
        return self::PARAM === $param;
    }

    public function choices(): bool
    {
        return false;
    }
}
