<?php

declare(strict_types=1);

namespace BaksDev\Support\UseCase\Admin\Add;

use BaksDev\Support\Entity\Message\SupportMessageInterface;
use BaksDev\Support\Type\Message\SupportMessageUid;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

/** @see SupportMessage */
final class SupportMessageAddDTO implements SupportMessageInterface
{
    private ?SupportMessageUid $id = null;

    /** Никнейм */
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $name;

    /** Текст сообщения */
    #[Assert\NotBlank]
    #[Assert\Length(max: 4096)]
    private ?string $message;

    /** Исходящее сообщение */
    #[Assert\NotBlank]
    private ?SupportMessageDTO $reply;

    private ?string $title;

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

    public function setDate(DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    public function getId(): ?SupportMessageUid
    {
        return $this->id;
    }

    public function setId(?SupportMessageUid $id): void
    {
        $this->id = $id;
    }

    public function getReply(): ?SupportMessageDTO
    {
        return $this->reply;
    }

    public function setReply(?SupportMessageDTO $reply): void
    {
        $this->reply = $reply;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

}
