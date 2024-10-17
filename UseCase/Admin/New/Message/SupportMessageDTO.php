<?php

declare(strict_types=1);

namespace BaksDev\Support\UseCase\Admin\New\Message;

use BaksDev\Support\Entity\Message\SupportMessageInterface;
use BaksDev\Support\Type\Message\SupportMessageUid;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

/** @see SupportMessage */
final class SupportMessageDTO implements SupportMessageInterface
{
    private ?SupportMessageUid $id = null;

    /** Никнейм */
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $name = null;

    /** Текст сообщения */
    #[Assert\NotBlank]
    #[Assert\Length(max: 4096)]
    private ?string $message;

    #[Assert\NotBlank]
    private ?DateTimeImmutable $date;


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getId(): ?SupportMessageUid
    {
        return $this->id;
    }

    public function setId(?SupportMessageUid $id): void
    {
        $this->id = $id;
    }

}
