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

namespace BaksDev\Support\Repository\SupportCurrentEventByTicket;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Invariable\SupportInvariable;


final class CurrentSupportEventByTicketRepository implements CurrentSupportEventByTicketInterface
{
    private string|false $ticket = false;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function forTicket(string|int|null $ticket): self
    {
        if(null === $ticket)
        {
            return $this;
        }

        $this->ticket = (string) $ticket;

        return $this;
    }

    /**  Метод возвращает текущее активное событие  */
    public function find(): SupportEvent|false
    {
        if($this->ticket === false)
        {
            return false;
        }

        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);


        $orm
            ->from(SupportInvariable::class, 'invariable')
            ->where('invariable.ticket = :ticket')
            ->setParameter(
                key: 'ticket',
                value: $this->ticket
            );

        $orm
            ->select('event')
            ->leftJoin(
                SupportEvent::class,
                'event',
                'WITH',
                'event.id = invariable.event'
            );

        return $orm->getOneOrNullResult() ?: false;
    }
}