<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Status\SupportStatus;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.support.status')]
interface SupportStatusInterface
{
    /** Возвращает значение (value) */
    public function getValue(): string;

    /**  Сортировка (чем выше число - тем первым в итерации будет значение) */
    public static function priority(): int;

    /** Проверяет, относится ли статус к данному объекту */
    public static function equals(string $param): bool;

}
