<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Status\SupportStatus;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.support.status')]
interface SupportStatusInterface
{
    /** Возвращает значение (value) */
    public function getValue(): string;

    /** Возвращает значение по умолчанию */
    public function default(): string|bool;

    /** Метод указывает, нужно ли добавить свойство для заполнения в форму */
    public function isSetting(): bool;

    /** Обязательное для заполнения свойство */
    public function required(): bool;

    /**  Сортировка (чем выше число - тем первым в итерации будет значение) */
    public static function priority(): int;

    /** Проверяет, относится ли статус к данному объекту */
    public static function equals(string $param): bool;

    public function choices(): bool;

}
