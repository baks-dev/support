<?php


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
            ->join(
                'event',
                SupportInvariable::class,
                'invariable',
                'invariable.main = event.main AND invariable.event = event.id'
            );


        $dbal
            ->addSelect('message.id AS message_id')
            ->addSelect('message.name AS name')
            ->addSelect('message.message AS message')
            ->addSelect('message.date AS date')
            ->leftJoin(
                'event',
                SupportMessage::class,
                'message',
                'message.event = event.id'
            );

        return $dbal
            ->enableCache('support')
            ->fetchAllAssociative();
    }
}
