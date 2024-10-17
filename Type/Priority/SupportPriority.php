<?php

namespace BaksDev\Support\Type\Priority;

use BaksDev\Support\Type\Priority\SupportPriority\SupportPriorityInterface;
use InvalidArgumentException;

final class SupportPriority
{
    public const string TYPE = 'support_priority';

    private ?SupportPriorityInterface $property = null;

    public function __construct(SupportPriorityInterface|self|string $property)
    {
        if(is_string($property) && class_exists($property))
        {
            $instance = new $property();

            if($instance instanceof SupportPriorityInterface)
            {
                $this->property = $instance;
                return;
            }
        }

        if($property instanceof SupportPriorityInterface)
        {
            $this->property = $property;
            return;
        }

        if($property instanceof self)
        {
            $this->property = $property->getSupportPriority();
            return;
        }

        /** @var SupportPriorityInterface $declare */
        foreach(self::getDeclared() as $declare)
        {
            if($declare::equals($property))
            {
                $this->property = new $declare();
                return;
            }
        }

        throw new InvalidArgumentException(sprintf('Undefined Support Status %s', $property));
    }


    public function __toString(): string
    {
        return $this->property ? $this->property->getvalue() : '';
    }

    public function getSupportPriority(): ?SupportPriorityInterface
    {
        return $this->property;
    }

    public function getSupportPriorityValue(): string
    {
        return $this->property->getValue();
    }


    public static function cases(): array
    {
        $case = [];

        foreach(self::getDeclared() as $property)
        {
            /** @var SupportPriorityInterface $property */
            $class = new $property();
            $case[$class::priority()] = new self($class);
        }

        return $case;
    }

    public static function getDeclared(): array
    {
        return array_filter(
            get_declared_classes(),
            static function($className) {
                return in_array(SupportPriorityInterface::class, class_implements($className), true);
            }
        );
    }

    public function equals(mixed $property): bool
    {
        $property = new self($property);

        return $this->getSupportPriorityValue() === $property->getSupportPriorityValue();
    }
}
