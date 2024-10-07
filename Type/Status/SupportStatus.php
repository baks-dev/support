<?php

namespace BaksDev\Support\Type\Status;

use BaksDev\Support\Type\Status\SupportStatus\SupportStatusInterface;

final class SupportStatus
{
    public const TYPE = 'support_status';

    private ?SupportStatusInterface $property = null;

    public function __construct(SupportStatusInterface|self|string $property)
    {

        if(is_string($property) && class_exists($property))
        {
            $instance = new $property();

            if($instance instanceof SupportStatusInterface)
            {
                $this->property = $instance;
                return;
            }
        }

        if($property instanceof SupportStatusInterface)
        {
            $this->property = $property;
            return;
        }

        if($property instanceof self)
        {
            $this->property = $property->getSupportStatus();
            return;
        }

        /** @var SupportStatusInterface $declare */
        foreach(self::getDeclared() as $declare)
        {
            if($declare::equals($property))
            {
                $this->property = new $declare();
                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('Undefined Support Status %s', $property));
    }


    public function __toString(): string
    {
        return $this->property ? $this->property->getvalue() : '';
    }

    public function getSupportStatus(): ?SupportStatusInterface
    {
        return $this->property;
    }

    public function getSupportStatusValue(): string
    {
        return $this->property->getValue();
    }


    public static function cases(): array
    {
        $case = [];

        foreach(self::getDeclared() as $property)
        {
            /** @var SupportStatusInterface $property */
            $class = new $property();
            $case[$class::priority()] = new self($class);
        }

        return $case;
    }

    public static function getDeclared(): array
    {
        return array_filter(
            get_declared_classes(),
            static function ($className) {
                return in_array(SupportStatusInterface::class, class_implements($className), true);
            }
        );
    }

    public function equals(mixed $property): bool
    {
        $property = new self($property);

        return $this->getSupportStatusValue() === $property->getSupportStatusValue();
    }
}
