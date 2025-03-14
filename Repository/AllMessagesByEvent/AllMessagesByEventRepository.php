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

namespace BaksDev\Support\Repository\AllMessagesByEvent;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Invariable\SupportInvariable;
use BaksDev\Support\Entity\Message\SupportMessage;
use BaksDev\Support\Type\Event\SupportEventUid;
use InvalidArgumentException;

final class AllMessagesByEventRepository implements AllMessagesByEventInterface
{
    private SupportEventUid|false $event = false;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
    ) {}

    public function forSupportEvent(SupportEvent|SupportEventUid|string $event): self
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

    /** Метод возвращает все сообщения по id тикета */
    public function findAll(): array|false
    {
        if($this->event === false)
        {
            throw new InvalidArgumentException('Invalid Argument $event');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        $dbal
            ->addSelect('event.id AS event')
            ->addSelect('event.status AS status')
            ->addSelect('event.priority AS priority')
            ->from(SupportEvent::class, 'event')
            ->where('event.id = :event')
            ->setParameter('event', $this->event, SupportEventUid::TYPE);


        $dbal
            ->addSelect('invariable.ticket')
            ->addSelect('invariable.title')
            ->addSelect('invariable.reply')
            ->join(
                'event',
                SupportInvariable::class,
                'invariable',
                'invariable.main = event.main AND invariable.event = event.id'
            );


        $dbal
            ->addSelect('message.id AS message_id')
            ->addSelect('message.external AS external')
            ->addSelect('message.name AS name')
            ->addSelect('message.message AS message')
            ->addSelect('message.date AS date')
            ->addSelect('message.out AS out')
            ->leftJoin(
                'event',
                SupportMessage::class,
                'message',
                'message.event = event.id'
            );

        $dbal->addOrderBy('message.date', 'ASC');

        return $dbal
            ->enableCache('support')
            ->fetchAllAssociative();
    }
}