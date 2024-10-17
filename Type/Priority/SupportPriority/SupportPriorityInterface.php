<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Priority\SupportPriority;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.support.priority')]
interface SupportPriorityInterface
{
    /** Возвращает значение (value) */
    public function getValue(): string;

    /** Сортировка (чем выше число - тем первым в итерации будет значение) */
    public static function priority(): int;

    /** Проверяет, относится ли статус к данному объекту */
    public static function equals(string $param): bool;

}
