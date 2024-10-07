<?php

namespace BaksDev\Support\Type\Status;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Type;

final class SupportStatusType extends Type
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return (string) new SupportStatus($value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?SupportStatus
    {
        return !empty($value) ? new SupportStatus($value) : null;
    }

    public function getName(): string
    {
        return SupportStatus::TYPE;
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
