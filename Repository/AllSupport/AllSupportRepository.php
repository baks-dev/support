<?php


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
