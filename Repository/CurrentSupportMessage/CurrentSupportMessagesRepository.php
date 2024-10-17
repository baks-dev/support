<?php

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

    public function execute(): SupportMessage|false
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