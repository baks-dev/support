<?php

namespace BaksDev\Support\Type\Ticket\SupportTicket;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.support.ticket')]
interface SupportTicketInterface
{
    const string PREFIX = 'IN-';

    public function getTicket(): string;

    public static function equals(mixed $ticket): bool;

}