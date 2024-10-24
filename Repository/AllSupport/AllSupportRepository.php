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

namespace BaksDev\Support\Repository\AllSupport;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Invariable\SupportInvariable;
use BaksDev\Support\Entity\Message\SupportMessage;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatus\Collection\SupportStatusOpen;

final class AllSupportRepository implements AllSupportInterface
{
    private SearchDTO|false $search = false;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    /** Метод возвращает пагинатор Support */
    public function findPaginator(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('support.id')
            ->from(Support::class, 'support');

        $dbal
            ->addSelect('event.id AS event')
            ->addSelect('event.status AS status')
            ->addSelect('event.priority AS priority')
            ->join(
                'support',
                SupportEvent::class,
                'event',
                'event.id = support.event AND event.status = :status'
            )
            ->setParameter(
                'status',
                SupportStatusOpen::class,
                SupportStatus::TYPE
            );

        $dbal
            ->addSelect('invariable.ticket')
            ->addSelect('invariable.title')
            ->leftJoin(
                'support',
                SupportInvariable::class,
                'invariable',
                'invariable.main = support.id'
            );


        $dbal
            ->addSelect('message.name AS name')
            ->addSelect('message.message AS message')
            ->addSelect('message.id AS message_id')
            ->leftOneJoin(
                'support',
                SupportMessage::class,
                'message',
                'message.event = support.event'
            );


        /* Поиск */
        if($this->search->getQuery())
        {

            $dbal
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('invariable.title')
                ->addSearchLike('message.name')
                ->addSearchLike('message.message')
                ->addSearchLike('invariable.ticket');

        }

        $dbal->addOrderBy('event.priority');

        return $this->paginator->fetchAllAssociative($dbal);
    }
}