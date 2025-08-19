<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Support\Repository\SupportLastMessage;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Support\Entity\Invariable\SupportInvariable;
use BaksDev\Support\Entity\Message\SupportMessage;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Type\Id\SupportUid;
use InvalidArgumentException;

final class SupportLastMessageRepository implements SupportLastMessageInterface
{
    private SupportUid|false $support = false;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function forSupport(Support|SupportUid|string $support): self
    {
        if($support instanceof Support)
        {
            $support = $support->getId();
        }

        if(is_string($support))
        {
            $support = new SupportUid($support);
        }

        $this->support = $support;

        return $this;
    }

    /**
     * Метод возвращает идентификатор последнего сообщения тикета
     */
    public function find(): SupportLastMessageResult|false
    {
        if(false === ($this->support instanceof SupportUid))
        {
            throw new InvalidArgumentException('Invalid Argument $support');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('support.id')
            ->from(Support::class, 'support')
            ->where('support.id = :support')
            ->setParameter('support', $this->support, SupportUid::TYPE);

        $dbal
            ->addSelect('message.name AS name')
            ->addSelect('message.message AS message')
            ->addSelect('message.id AS message_id')
            ->addSelect('message.date AS date')
            ->addSelect('message.out AS out')
            ->leftOneJoin(
                'support',
                SupportMessage::class,
                'message',
                'message.event = support.event',
                sort: 'date'
            );

        $dbal
            ->addSelect('invariable.profile')
            ->leftJoin(
                'support',
                SupportInvariable::class,
                'invariable',
                'invariable.event = support.event',
            );

        $result = $dbal->fetchHydrate(SupportLastMessageResult::class);

        return ($result instanceof SupportLastMessageResult) ? $result : false;

    }
}