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

namespace BaksDev\Support\Repository\FindTicket;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Support\Entity\Invariable\SupportInvariable;
use BaksDev\Support\Entity\Support;


final class FindExistTicketRepository implements FindExistTicketInterface
{

    private string|false $ticket = false;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
    ) {}

    public function forTicket(?string $ticket): self
    {
        if(null === $ticket)
        {
            return $this;
        }

        $this->ticket = $ticket;

        return $this;
    }


    /**
     * Метод возвращает все сообщения по id тикета
     */
    public function exist(): bool
    {
        if($this->ticket === false)
        {
            return false;
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->from(SupportInvariable::class, 'invariable')
            ->where(' invariable.ticket = :ticket')
            ->setParameter('ticket', $this->ticket);

        $dbal
            ->join(
                'invariable',
                Support::class,
                'support',
                'invariable.main = support.id'
            );


        return $dbal->fetchExist();
    }

}