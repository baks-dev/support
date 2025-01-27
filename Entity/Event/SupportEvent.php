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

namespace BaksDev\Support\Entity\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Support\Entity\Invariable\SupportInvariable;
use BaksDev\Support\Entity\Message\SupportMessage;
use BaksDev\Support\Entity\Modify\SupportModify;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Id\SupportUid;
use BaksDev\Support\Type\Priority\SupportPriority;
use BaksDev\Support\Type\Status\SupportStatus;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* SupportEvent */

#[ORM\Entity]
#[ORM\Table(name: 'support_event')]
#[ORM\Index(columns: ['status', 'priority'])]
class SupportEvent extends EntityEvent
{
    /**  Идентификатор События */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: SupportEventUid::TYPE)]
    private SupportEventUid $id;

    /**  Идентификатор корня */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: SupportUid::TYPE)]
    private SupportUid $main;

    /** Статус тикета */
    #[Assert\NotBlank]
    #[ORM\Column(type: SupportStatus::TYPE)]
    private SupportStatus $status;


    /** Приоритет тикета */
    #[Assert\NotBlank]
    #[ORM\Column(type: SupportPriority::TYPE)]
    private SupportPriority $priority;


    /**  Модификатор */
    #[ORM\OneToOne(targetEntity: SupportModify::class, mappedBy: 'event', cascade: ['all'])]
    private SupportModify $modify;

    /**  Постоянная величина */
    #[ORM\OneToOne(targetEntity: SupportInvariable::class, mappedBy: 'event', cascade: ['all'])]
    private ?SupportInvariable $invariable = null;

    public function getInvariable(): ?SupportInvariable
    {
        return $this->invariable;
    }


    /** Сообщения (сортируем по дате) */
    #[Assert\Valid]
    #[ORM\OrderBy(["date" => "ASC"])]
    #[ORM\OneToMany(targetEntity: SupportMessage::class, mappedBy: 'event', cascade: ['all'])]
    private Collection $messages;


    public function __construct()
    {
        $this->id = new SupportEventUid();
        $this->modify = new SupportModify($this);

    }

    /**  Идентификатор события */
    public function __clone()
    {
        $this->id = clone new SupportEventUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): SupportEventUid
    {
        return $this->id;
    }

    /** Идентификатор Support */
    public function setMain(SupportUid|Support $main): void
    {
        $this->main = $main instanceof Support ? $main->getId() : $main;
    }

    public function getMain(): ?SupportUid
    {
        return $this->main;
    }

    public function getTitle(): ?string
    {
        return $this->invariable?->getTitle();
    }

    public function getDto($dto): mixed
    {
        if($dto instanceof SupportEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof SupportEventInterface)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}