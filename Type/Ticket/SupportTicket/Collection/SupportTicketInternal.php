<?php

declare(strict_types=1);

namespace BaksDev\Support\Type\Ticket\SupportTicket\Collection;

use BaksDev\Support\Type\Ticket\SupportTicket\SupportTicketInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.support.ticket')]
final class SupportTicketInternal implements SupportTicketInterface
{
    const string PREFIX = 'IN';

    private string|int $ticket;

    public function __construct(int|string|null $ticket = null)
    {
        if(is_null($ticket))
        {
            /** Генерируем идентификатор тикета */
            $ticket = number_format(
                (microtime(true) * 100),
                0,
                '.',
                '.'
            );
        }

        $this->ticket = $ticket;
    }

    public function getTicket(): string
    {
        return sprintf('%s-%s', self::PREFIX, $this->ticket);
    }

    public static function equals(mixed $ticket): bool
    {
        $ticket = (string) $ticket;
        $ticket = (string) mb_strtolower($ticket);
        return str_starts_with(mb_strtolower(self::PREFIX), $ticket);
    }
}