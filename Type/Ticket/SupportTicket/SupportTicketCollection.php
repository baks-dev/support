<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Ticket\SupportTicket;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class SupportTicketCollection
{
    public function __construct(
        #[AutowireIterator('baks.support.ticket')] private iterable $ticket,
    ) {}

    public function cases(): array
    {
        $case = null;

        foreach($this->ticket as $key => $ticket)
        {
            $case[$key] = new $ticket();
        }

        return $case;
    }
}