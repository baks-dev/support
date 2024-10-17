<?php

declare(strict_types=1);

namespace BaksDev\Support\Entity\Invariable;

use BaksDev\Core\Entity\EntityReadonly;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Type\Id\SupportUid;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* SupportInvariable */

#[ORM\Entity]
#[ORM\Table(name: 'support_invariable')]
class SupportInvariable extends EntityReadonly
{
    /**  Идентификатор Main */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: SupportUid::TYPE)]
    private SupportUid $main;

    /** Идентификатор События */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\OneToOne(targetEntity: SupportEvent::class, inversedBy: 'invariable')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private SupportEvent $event;


    /** Тип профиля */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Column(type: TypeProfileUid::TYPE)]
    private TypeProfileUid $type;


    /**  Профиль */
    #[Assert\Uuid]
    #[ORM\Column(type: UserProfileUid::TYPE, nullable: true)]
    private ?UserProfileUid $profile = null;

    /** Id Тикета */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING)]
    private string $ticket;

    /** Тема, заголовок или иная информация о предмете сообщения */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING)]
    private ?string $title = null;

    public function __construct(SupportEvent $event)
    {
        $this->event = $event;
        $this->main = $event->getMain();

        /** Генерируем идентификатор тикета */
        $this->ticket = number_format(
            (microtime(true) * 100),
            0,
            '.',
            '.'
        );
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof SupportInvariableInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if($dto instanceof SupportInvariableInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}
