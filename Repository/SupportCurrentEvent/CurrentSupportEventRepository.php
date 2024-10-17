<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Support\Repository\SupportCurrentEvent;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Type\Id\SupportUid;

final class CurrentSupportEventRepository implements CurrentSupportEventInterface
{
    private SupportUid|false $supportUid = false;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

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

        $this->supportUid = $support;

        return $this;
    }

    /**  Метод возвращает текущее активное событие  */
    public function find(): SupportEvent|false
    {
        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm
            ->from(Support::class, 'support')
            ->where('support.id = :support')
            ->setParameter(
                'support',
                $this->supportUid,
                SupportUid::TYPE
            );


        $orm
            ->select('event')
            ->join(
                SupportEvent::class,
                'event',
                'WITH',
                'event.id = support.event'
            );

        return $orm->getOneOrNullResult() ?: false;
    }
}
