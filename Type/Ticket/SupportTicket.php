<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Ticket;

use BaksDev\Support\Type\Ticket\SupportTicket\SupportTicketInterface;
use InvalidArgumentException;

final class SupportTicket
{
    public const string TYPE = 'support_ticket';

    private ?SupportTicketInterface $ticket = null;

    public function __construct(SupportTicketInterface|self|string $ticket)
    {

        if(is_string($ticket) && class_exists($ticket))
        {
            $instance = new $ticket();

            if($instance instanceof SupportTicketInterface)
            {
                $this->ticket = $instance;
                return;
            }
        }

        if($ticket instanceof SupportTicketInterface)
        {
            $this->ticket = $ticket;
            return;
        }

        if($ticket instanceof self)
        {
            $this->ticket = $ticket->getSupportTicket();
            return;
        }

        /** @var SupportTicketInterface $declare */
        foreach(self::getDeclared() as $declare)
        {
            if($declare::equals($ticket))
            {
                $this->ticket = new $declare();
                return;
            }
        }

        throw new InvalidArgumentException(sprintf('Undefined Support Ticket %s', $ticket));
    }


    public function __toString(): string
    {
        return $this->ticket ? $this->ticket->getTicket() : '';
    }

    public function getSupportTicket(): ?SupportTicketInterface
    {
        return $this->ticket;
    }

    public function getSupportTicketValue(): string
    {
        return $this->ticket->getTicket();
    }

    public static function getDeclared(): array
    {
        return array_filter(
            get_declared_classes(),
            static function($className) {
                return in_array(SupportTicketInterface::class, class_implements($className), true);
            }
        );
    }

    public function equals(mixed $ticket): bool
    {
        $ticket = new self($ticket);

        return $this->getSupportTicketValue() === $ticket->getSupportTicketValue();
    }
}