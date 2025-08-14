<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Support\Entity\Token;

use BaksDev\Core\Entity\EntityReadonly;
use BaksDev\Core\Type\UidType\UidType;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Type\Id\SupportUid;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
#[ORM\Table(name: 'support_token')]
class SupportToken extends EntityReadonly
{
    /**
     * Идентификатор Main
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: SupportUid::TYPE)]
    private SupportUid $main;

    /**
     * Идентификатор События
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\OneToOne(targetEntity: SupportEvent::class, inversedBy: 'token')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private SupportEvent $event;

    /** Токен авторизации */
    #[ORM\Column(type: UidType::TYPE, nullable: true)]
    private ?string $value = null;


    public function __construct(SupportEvent $event)
    {
        $this->event = $event;
        $this->main = $event->getMain();
    }

    public function __toString(): string
    {
        return (string) $this->main;
    }

    public function setEvent(SupportEvent $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof SupportTokenInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if($dto instanceof SupportTokenInterface || $dto instanceof self)
        {
            if(empty($dto->getValue()))
            {
                return false;
            }

            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}