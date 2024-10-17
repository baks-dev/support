<?php

declare(strict_types=1);

namespace BaksDev\Support\UseCase\Admin\New\Invariable;

use BaksDev\Support\Entity\Invariable\SupportInvariableInterface;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Symfony\Component\Validator\Constraints as Assert;

/** @see SupportInvariable */
final class SupportInvariableDTO implements SupportInvariableInterface
{
    /** Тип профиля */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    private TypeProfileUid $type;

    /**  Профиль */
    #[Assert\Uuid]
    private ?UserProfileUid $profile = null;

    /** Id Тикета */
    #[Assert\NotBlank]
    private string $ticket;

    /** Тема, предмет сообщений тикета */
    #[Assert\NotBlank]
    private string $title;


    public function getType(): TypeProfileUid
    {
        return $this->type;
    }

    public function setType(TypeProfileUid $type): void
    {
        $this->type = $type;
    }

    public function getProfile(): ?UserProfileUid
    {
        return $this->profile;
    }

    public function setProfile(?UserProfileUid $profile): void
    {
        $this->profile = $profile;
    }

    public function getTicket(): string
    {
        return $this->ticket;
    }

    public function setTicket(string $ticket): void
    {
        $this->ticket = $ticket;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

}
