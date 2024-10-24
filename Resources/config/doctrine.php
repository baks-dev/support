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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Support\BaksDevSupportBundle;
use BaksDev\Support\Type\Event\SupportEventType;
use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Id\SupportType;
use BaksDev\Support\Type\Id\SupportUid;
use BaksDev\Support\Type\Message\SupportMessageType;
use BaksDev\Support\Type\Message\SupportMessageUid;
use BaksDev\Support\Type\Priority\SupportPriority;
use BaksDev\Support\Type\Priority\SupportPriorityType;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatusType;
use BaksDev\Support\Type\Ticket\SupportTicket;
use BaksDev\Support\Type\Ticket\SupportTicketType;
use Symfony\Config\DoctrineConfig;

return static function(DoctrineConfig $doctrine, ContainerConfigurator $configurator): void {

    $doctrine->dbal()->type(SupportUid::TYPE)->class(SupportType::class);
    $doctrine->dbal()->type(SupportEventUid::TYPE)->class(SupportEventType::class);
    $doctrine->dbal()->type(SupportMessageUid::TYPE)->class(SupportMessageType::class);
    $doctrine->dbal()->type(SupportStatus::TYPE)->class(SupportStatusType::class);
    $doctrine->dbal()->type(SupportPriority::TYPE)->class(SupportPriorityType::class);


    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);

    $emDefault
        ->mapping('support')
        ->type('attribute')
        ->dir(BaksDevSupportBundle::PATH.'Entity')
        ->isBundle(false)
        ->prefix(BaksDevSupportBundle::NAMESPACE.'\\Entity')
        ->alias('support');
};