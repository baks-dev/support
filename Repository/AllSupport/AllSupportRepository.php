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

namespace BaksDev\Support\Repository\AllSupport;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Invariable\SupportInvariable;
use BaksDev\Support\Entity\Message\SupportMessage;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Form\Admin\Index\SupportTicketStatusFilterDTO;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatus\Collection\SupportStatusOpen;
use BaksDev\Users\Profile\TypeProfile\Entity\Trans\TypeProfileTrans;
use BaksDev\Users\Profile\TypeProfile\Entity\TypeProfile;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Repository\UserProfileTokenStorage\UserProfileTokenStorageInterface;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;

final class AllSupportRepository implements AllSupportInterface
{
    private SearchDTO|false $search = false;

    private SupportTicketStatusFilterDTO|false $filter = false;

    private UserProfileUid|false $profile = false;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
        private readonly UserProfileTokenStorageInterface $UserProfileTokenStorage
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function filter(SupportTicketStatusFilterDTO $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    public function profile(UserProfile|UserProfileUid|string $profile): self
    {
        if($profile instanceof UserProfile)
        {
            $profile = $profile->getId();
        }

        if(is_string($profile))
        {
            $profile = new UserProfileUid($profile);
        }

        $this->profile = $profile;

        return $this;
    }

    /** Метод возвращает пагинатор Support */
    public function findPaginator(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal
            ->select('support.id')
            ->addSelect('support.event')
            ->from(Support::class, 'support');

        $dbal
            ->addSelect('event.status AS status')
            ->addSelect('event.priority AS priority')
            ->join(
                'support',
                SupportEvent::class,
                'event',
                'event.id = support.event',
            );

        if($this->filter)
        {
            if(is_null($this->filter->getStatus()))
            {
                $this->filter->setStatus(new SupportStatusOpen());
            }

            $dbal
                ->andWhere('event.status = :status')
                ->setParameter(
                    'status',
                    $this->filter->getStatus(),
                    SupportStatus::TYPE,
                );
        }

        $dbal
            ->addSelect('invariable.ticket')
            ->addSelect('invariable.title')
            ->join(
                'support',
                SupportInvariable::class,
                'invariable',

                /**
                 * TODO: добавить соответствующий фильтр !!!
                 * AND (invariable.profile = :profile OR invariable.profile IS NULL)
                 */

                'invariable.main = support.id',

            )
            ->setParameter(
                'profile',
                $this->profile ?: $this->UserProfileTokenStorage->getProfile(),
                UserProfileUid::TYPE,
            );


        $dbal
            ->leftJoin(
                'invariable',
                TypeProfile::class,
                'type_profile',
                'type_profile.id = invariable.type',
            );

        $dbal
            ->addSelect('type_profile_trans.name AS type_profile_name')
            ->leftJoin(
                'type_profile',
                TypeProfileTrans::class,
                'type_profile_trans',
                'type_profile_trans.event = type_profile.event AND type_profile_trans.local = :local',
            );


        $dbal
            ->addSelect('message.name AS name')
            ->addSelect('message.message AS message')
            ->addSelect('message.id AS message_id')
            ->addSelect('message.date AS date')
            ->leftOneJoin(
                'support',
                SupportMessage::class,
                'message',
                'message.event = support.event',
                sort: 'date',
            );

        /* Поиск */
        if($this->search->getQuery())
        {
            $dbal
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('invariable.title')
                ->addSearchLike('type_profile_trans.name')
                ->addSearchLike('message.name')
                ->addSearchLike('message.message')
                ->addSearchLike('invariable.ticket');
        }

        if($this->filter && $this->filter->getStatus() instanceof SupportStatusOpen)
        {
            $dbal
                ->addOrderBy('event.priority')
                ->addOrderBy('event.id', 'ASC');
        }
        else
        {
            $dbal
                //->addOrderBy('message.date')
                ->addOrderBy('event.id', 'DESC');
        }


        return $this->paginator
            ->fetchAllHydrate($dbal, AllSupportResult::class);
    }

}