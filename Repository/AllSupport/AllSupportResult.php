<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Support\Repository\AllSupport;

use BaksDev\Ozon\Support\BaksDevOzonSupportBundle;
use BaksDev\Ozon\Support\Type\OzonQuestionProfileType;
use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Id\SupportUid;
use BaksDev\Support\Type\Message\SupportMessageUid;
use BaksDev\Yandex\Support\BaksDevYandexSupportBundle;
use BaksDev\Yandex\Support\Types\ProfileType\TypeProfileYandexQuestionSupport;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

/** @see AllSupportResult */
final class AllSupportResult
{
    public function __construct(
        private string $id, //" => "0197dc7d-0a43-7764-ac25-2a5bc7f94fd0"
        private string $event, //" => "0197dfb0-f599-78fb-b6f1-3ee799fda99c"
        private string $status, //" => "open"
        private string $priority, //" => "height"
        private string $ticket, //" => "u2i-3LpXN05j9eSa6Iks0OXs_A"
        private string $title, //" => "Triangle TH201 Sportex 245/45 R18 100Y"

        private ?string $type_profile_id, //" => "Авито сообщения"
        private ?string $type_profile_name, //" => "Авито сообщения"

        private ?string $name, //" => "Владимир"
        private ?string $message, //" => "и отзывы кстати хуже на 202"
        private ?string $message_id, //" => "0197dfb0-f5a0-7b78-9b0a-2ea88c7c013b"
        private ?string $date, //" => "2025-07-06 15:22:46"
        // сопутствующее производство
    ) {}

    public function getId(): SupportUid
    {
        return new SupportUid($this->id);
    }

    public function getEvent(): SupportEventUid
    {
        return new SupportEventUid($this->event);
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getTicket(): string
    {
        return $this->ticket;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTypeProfileId(): ?string
    {
        return $this->type_profile_id;
    }

    public function isClosedAction(): bool
    {
        /** Запрещаем закрывать тикет на Ozon Support Question «Вопрос» */
        if(
            class_exists(BaksDevOzonSupportBundle::class)
            && OzonQuestionProfileType::equals($this->type_profile_id)
        )
        {
            return false;
        }

        /** Запрещаем закрывать тикет на Yandex Support Question «Вопрос» */
        if(
            class_exists(BaksDevYandexSupportBundle::class)
            && TypeProfileYandexQuestionSupport::equals($this->type_profile_id)
        )
        {
            return false;
        }

        return true;
    }


    public function getTypeProfileName(): string
    {
        return $this->type_profile_name ?: 'Не определен';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMessage(): ?string
    {
        if(empty($this->message))
        {
            return null;
        }

        $message = strip_tags($this->message);
        $message = str_replace('Ссылка на товар', '', $message);

        return $message;
    }

    public function getMessageId(): SupportMessageUid
    {
        return new SupportMessageUid($this->message_id);
    }

    public function getMessageDate(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->date ?? 'now');
    }
}