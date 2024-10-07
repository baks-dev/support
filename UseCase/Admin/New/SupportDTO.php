<?php

declare(strict_types=1);

namespace BaksDev\Support\UseCase\Admin\New;

use BaksDev\Support\Entity\Event\SupportEventInterface;
use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Priority\SupportPriority;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\UseCase\Admin\New\Invariable\SupportInvariableDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @see SupportEvent */
final class SupportDTO implements SupportEventInterface
{
    /**  Идентификатор События */
    #[Assert\Uuid]
    private ?SupportEventUid $id = null;


    /** Постоянная величина */
    private ?SupportInvariableDTO $invariable = null;

    /** Статус тикета */
    #[Assert\NotBlank]
    private SupportStatus $status;


    /** Приоритет тикета */
    #[Assert\NotBlank]
    private SupportPriority $priority;


    /** Сообщения */
    private ArrayCollection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getEvent(): ?SupportEventUid
    {
        return $this->id;
    }


    public function setId(SupportEventUid $id): void
    {
        $this->id = $id;
    }

    public function getInvariable(): ?SupportInvariableDTO
    {
        return $this->invariable;
    }

    public function setInvariable(?SupportInvariableDTO $invariable): void
    {
        $this->invariable = $invariable;
    }

    public function getMessages(): ArrayCollection
    {
        return $this->messages;
    }


    public function addMessage(Message\SupportMessageDTO $message): void
    {
        $filter = $this->messages->filter(function (Message\SupportMessageDTO $element) use ($message) {
            return $message->getId()?->equals($element->getId());
        });

        if($filter->isEmpty())
        {
            $this->messages->add($message);
        }
    }

    public function removeProperty(Message\SupportMessageDTO $message): void
    {
        $this->messages->removeElement($message);
    }

    public function getStatus(): SupportStatus
    {
        return $this->status;
    }

    public function setStatus(SupportStatus $status): void
    {
        $this->status = $status;
    }

    public function getPriority(): SupportPriority
    {
        return $this->priority;
    }

    public function setPriority(SupportPriority $priority): void
    {
        $this->priority = $priority;
    }

}
