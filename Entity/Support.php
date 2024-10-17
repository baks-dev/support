<?php

declare(strict_types=1);

namespace BaksDev\Support\Entity;

use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Id\SupportUid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/* Support */

#[ORM\Entity]
#[ORM\Table(name: 'support')]
class Support
{
    /** Идентификатор сущности */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: SupportUid::TYPE)]
    private SupportUid $id;

    /** Идентификатор события */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: SupportEventUid::TYPE, unique: true)]
    private SupportEventUid $event;


    public function __construct()
    {
        $this->id = new SupportUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    /**
     * Идентификатор
     */
    public function getId(): SupportUid
    {
        return $this->id;
    }

    /**
     * Идентификатор события
     */
    public function getEvent(): SupportEventUid
    {
        return $this->event;
    }

    public function setEvent(SupportEventUid|SupportEvent $event): void
    {
        $this->event = $event instanceof SupportEvent ? $event->getId() : $event;
    }
}
