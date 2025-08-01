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

namespace BaksDev\Support\Repository\SupportInvariable;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Support\Entity\Invariable\SupportInvariable;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Type\Id\SupportUid;
use InvalidArgumentException;


final class SupportInvariableRepository implements SupportInvariableInterface
{
    private SupportUid|false $support;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function forSupport(Support|SupportUid $support): self
    {

        if($support instanceof Support)
        {
            $support = $support->getId();
        }

        $this->support = $support;

        return $this;
    }

    /**
     * Метод получает объект OrderInvariable
     */
    public function find(): SupportInvariable|false
    {
        if(false === ($this->support instanceof SupportUid))
        {
            throw new InvalidArgumentException('Invalid Argument Support');
        }

        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm
            ->select('invariable')
            ->from(SupportInvariable::class, 'invariable')
            ->where('invariable.main = :main')
            ->setParameter(
                key: 'main',
                value: $this->support,
                type: SupportUid::TYPE,
            );

        return $orm->getQuery()->getOneOrNullResult() ?? false;
    }
}