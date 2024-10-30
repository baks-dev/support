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

namespace BaksDev\Support\Repository\CurrentSupportMessage;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Message\SupportMessage;
use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Message\SupportMessageUid;
use InvalidArgumentException;

final class CurrentSupportMessagesRepository implements CurrentSupportMessagesInterface
{
    private SupportMessageUid|false $message = false;

    private SupportEventUid|false $event = false;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function forMessage(SupportMessageUid|string $supportMessageUid): self
    {
        if(is_string($supportMessageUid))
        {
            $supportMessageUid = new SupportMessageUid($supportMessageUid);
        }

        $this->message = $supportMessageUid;

        return $this;
    }

    public function forEvent(SupportEvent|SupportEventUid|string $event): self
    {

        if($event instanceof SupportEvent)
        {
            $event = $event->getId();
        }

        if(is_string($event))
        {
            $event = new SupportEventUid($event);
        }

        $this->event = $event;

        return $this;
    }

    public function find(): SupportMessage|false
    {
        if($this->event === false)
        {
            throw new InvalidArgumentException('Invalid Argument SupportEventUid');
        }

        if($this->message === false)
        {
            throw new InvalidArgumentException('Invalid Argument SupportMessageUid');
        }

        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm
            ->addSelect('message')
            ->from(SupportMessage::class, 'message')
            ->where('message.event = :event')
            ->andWhere('message.id = :id')
            ->setParameter(
                'event',
                $this->event,
                SupportEventUid::TYPE
            )
            ->setParameter(
                'id',
                $this->message,
                SupportMessageUid::TYPE
            );

        return $orm->getOneOrNullResult() ?: false;
    }
}