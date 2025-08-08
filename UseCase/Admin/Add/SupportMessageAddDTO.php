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

namespace BaksDev\Support\UseCase\Admin\Add;

use BaksDev\Support\Entity\Message\SupportMessageInterface;
use BaksDev\Support\Type\Message\SupportMessageUid;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

/** @see SupportMessage */
final class SupportMessageAddDTO implements SupportMessageInterface
{
    private ?SupportMessageUid $id = null;

    /** Никнейм */
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    /** Текст сообщения */
    #[Assert\NotBlank]
    private ?string $message;

    /** Исходящее сообщение */
    #[Assert\NotBlank]
    private ?SupportMessageDTO $reply;

    /** Тема тикета для UI, не сохраняется в БД*/
    private ?string $title;

    #[Assert\NotBlank]
    private ?DateTimeImmutable $date;

    /** Является ли сообщение исходящим */
    private bool $out;

    /** Флаг запрета на ответ */
    private bool $submit = true;

    /** Тип сообщений (для быстрых ответов) */
    private ?TypeProfileUid $type = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name ?: 'Покупатель';
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        /** Форматируем сообщение */
        $array = explode(PHP_EOL, $message);
        $array = array_map('trim', $array);
        $array = array_filter($array, 'strlen');
        $message = implode(PHP_EOL.PHP_EOL, $array);

        $this->message = $message;
        return $this;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?DateTimeImmutable $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getId(): ?SupportMessageUid
    {
        return $this->id;
    }

    public function setId(?SupportMessageUid $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getReply(): ?SupportMessageDTO
    {
        return $this->reply;
    }

    public function setReply(?SupportMessageDTO $reply): self
    {
        $this->reply = $reply;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function isOut(): bool
    {
        return $this->out;
    }

    public function setOutMessage(): self
    {
        $this->out = true;
        return $this;
    }

    public function setInMessage(): self
    {
        $this->out = false;
        return $this;
    }

    /**
     * Submit
     */
    public function isSubmit(): bool
    {
        return $this->submit;
    }

    public function notSubmit(): self
    {
        $this->submit = false;

        return $this;
    }

    public function getTicketType(): ?TypeProfileUid
    {
        return $this->type;
    }

    public function setTicketType(?TypeProfileUid $type): self
    {
        $this->type = $type;
        return $this;
    }
}