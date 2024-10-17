<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Message;

use BaksDev\Core\Type\UidType\Uid;
use Symfony\Component\Uid\AbstractUid;

final class SupportMessageUid extends Uid
{
    /** Тестовый идентификатор */
    public const string TEST = 'b45ecdc9-8a44-737c-8501-79d3ba862031';

    public const string TYPE = 'support_message';

    private mixed $attr;

    private mixed $option;

    private mixed $property;

    private mixed $characteristic;


    public function __construct(
        AbstractUid|string|null $value = null,
        mixed $attr = null,
        mixed $option = null,
        mixed $property = null,
        mixed $characteristic = null,
    )
    {
        parent::__construct($value);

        $this->attr = $attr;
        $this->option = $option;
        $this->property = $property;
        $this->characteristic = $characteristic;
    }


    public function getAttr(): mixed
    {
        return $this->attr;
    }


    public function getOption(): mixed
    {
        return $this->option;
    }


    public function getProperty(): mixed
    {
        return $this->property;
    }

    public function getCharacteristic(): mixed
    {
        return $this->characteristic;
    }

}
