<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
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
 *
 */

declare(strict_types=1);

namespace BaksDev\Support\Messenger\Notification;

use BaksDev\Core\Deduplicator\DeduplicatorInterface;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Repository\ExistTicket\ExistSupportTicketInterface;
use BaksDev\Support\Type\Priority\SupportPriority;
use BaksDev\Support\Type\Priority\SupportPriority\Collection\SupportPriorityLow;
use BaksDev\Support\Type\Profile\SupportNotificationProfileType;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatus\Collection\SupportStatusOpen;
use BaksDev\Support\UseCase\Admin\New\Invariable\SupportInvariableDTO;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;
use BaksDev\Support\UseCase\Admin\New\SupportHandler;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Сохраняет переданные данные как системное уведомление
 * @see SupportNotificationProfileType
 */
#[Autoconfigure(shared: false)]
#[AsMessageHandler(priority: 0)]
final readonly class AddSupportNotificationDispatcher
{
    public function __construct(
        #[Target('supportLogger')] private LoggerInterface $logger,
        private DeduplicatorInterface $Deduplicator,
        private SupportHandler $supportHandler,
        private ExistSupportTicketInterface $existSupportTicketRepository,
    ) {}

    public function __invoke(AddSupportNotificationMessage $message): void
    {
        $Deduplicator = $this->Deduplicator
            ->namespace('support')
            ->deduplication([
                var_export($message, true),
                self::class
            ]);

        if($Deduplicator->isExecuted())
        {
            return;
        }

        $data = $message->getData();
        $profile = $message->getProfile();
        $hashData = md5(var_export($message, true));

        $ticketExist = $this->existSupportTicketRepository
            ->ticket($hashData)
            ->exist();

        if(true === $ticketExist)
        {
            $this->logger->warning(
                message: 'Уведомление уже было добавлено',
                context: [
                    self::class.':'.__LINE__,
                    $message,
                ],
            );

            return;
        }

        /**
         * @var object{
         *     type: string,
         *     header: string,
         *     message: string,
         *     identifier: string,
         * } $notification
         */
        $notification = json_decode($data);

        /** SupportDTO */
        $SupportDTO = new SupportDTO();

        $SupportDTO
            ->setPriority(new SupportPriority(SupportPriorityLow::PARAM))
            ->setStatus(new SupportStatus(SupportStatusOpen::PARAM))
            ->getToken()->setValue($profile); // Присваиваем токен для последующего поиска

        /** SupportInvariableDTO */
        $SupportInvariableDTO = new SupportInvariableDTO();
        $SupportInvariableDTO
            ->setProfile($profile)
            ->setType(new TypeProfileUid(SupportNotificationProfileType::class));

        $SupportInvariableDTO
            ->setTicket($hashData)
            ->setTitle($notification->header);

        $SupportDTO->setInvariable($SupportInvariableDTO);

        /** SupportMessageDTO */
        $SupportMessageDTO = new SupportMessageDTO();

        $SupportMessageDTO
            ->setName('baks-dev')
            ->setMessage($notification->message)
            ->setDate(new DateTimeImmutable())
            ->setInMessage(); //  Внутреннее сообщение

        $SupportDTO->addMessage($SupportMessageDTO);

        $Support = $this->supportHandler->handle($SupportDTO);

        if(false === $Support instanceof Support)
        {
            $this->logger->critical(
                message: sprintf('%s Ошибка при сохранении уведомления',
                    $Support
                ),
                context: [
                    self::class.':'.__LINE__,
                    $message,
                ],
            );

            return;
        }

        $Deduplicator->save();
    }
}