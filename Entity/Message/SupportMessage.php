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

declare(strict_types=1);

namespace BaksDev\Support\Entity\Message;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Type\Message\SupportMessageUid;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* SupportMessage */

#[ORM\Entity]
#[ORM\Table(name: 'support_message')]
class SupportMessage extends EntityEvent
{
    /** Идентификатор сущности */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: SupportMessageUid::TYPE)]
    private SupportMessageUid $id;

    /** Связь на событие */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\ManyToOne(targetEntity: SupportEvent::class, inversedBy: "messages")]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: "id")]
    private SupportEvent $event;

    /** Никнейм */
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING)]
    private string $name;

    /** Текст сообщения */
    #[Assert\NotBlank]
    #[Assert\Length(max: 4096)]
    #[ORM\Column(type: Types::TEXT)]
    private string $message;

    /** Дата */
    #[Assert\NotBlank]
    #[ORM\Column(name: 'date', type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $date;


    public function __construct(SupportEvent $event)
    {
        $this->id = new SupportMessageUid();
        $this->event = $event;
        $this->date = new DateTimeImmutable();
    }

    public function __clone(): void
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->event;
    }

    public function getDto($dto): mixed
    {
        if($dto instanceof SupportMessageInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {

        if($dto instanceof SupportMessageInterface)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}