<?php

namespace BaksDev\Support\Type\Priority;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class SupportPriorityType extends Type
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return (string) new SupportPriority($value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?SupportPriority
    {
        return !empty($value) ? new SupportPriority($value) : null;
    }

    public function getName(): string
    {
        return SupportPriority::TYPE;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }
}
