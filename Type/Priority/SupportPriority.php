<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

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