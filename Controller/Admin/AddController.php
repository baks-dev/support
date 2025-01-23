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

namespace BaksDev\Support\Controller\Admin;

use BaksDev\Centrifugo\Server\Publish\CentrifugoPublishInterface;
use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Core\Type\UidType\ParamConverter;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Repository\CurrentSupportMessage\CurrentSupportMessagesInterface;
use BaksDev\Support\Type\Message\SupportMessageUid;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatus\Collection\SupportStatusClose;
use BaksDev\Support\UseCase\Admin\Add\SupportMessageAddDTO;
use BaksDev\Support\UseCase\Admin\Add\SupportMessageAddForm;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;
use BaksDev\Support\UseCase\Admin\New\SupportHandler;
use BaksDev\Users\Profile\UserProfile\Repository\CurrentUserProfile\CurrentUserProfileInterface;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_SUPPORT_ADD')]
final class AddController extends AbstractController
{
    #[Route('/admin/support/message/add/{id}/{message}', name: 'admin.add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        SupportHandler $SupportHandler,
        CurrentSupportMessagesInterface $currentSupportMessages,
        CurrentUserProfileInterface $currentUserProfileDTO,
        CentrifugoPublishInterface $publish,
        #[ParamConverter(SupportMessageUid::class)] $message,
        #[MapEntity] SupportEvent $SupportEvent,
    ): Response
    {

        $user = $currentUserProfileDTO->fetchProfileAssociative($this->getCurrentUsr());

        $SupportMessage = $currentSupportMessages
            ->forMessage($message)
            ->forEvent($SupportEvent)
            ->find();

        /** Входящее сообщение */
        $ReplySupportMessageDto = new SupportMessageAddDTO();
        $SupportMessage->getDto($ReplySupportMessageDto);

        /** Исходящее сообщение */
        $SupportMessageDTO = new SupportMessageDTO();

        /** Присваиваем имя профиля */
        $SupportMessageDTO
            ->setName($user['profile_username'] ?? null)
            ->setDate(new DateTimeImmutable());

        /** Тема тикета */
        $ReplySupportMessageDto
            ->setTitle($SupportEvent->getTitle())
            ->setOutMessage()
            ->setReply($SupportMessageDTO);

        // Форма
        $form = $this
            ->createForm(SupportMessageAddForm::class, $ReplySupportMessageDto, [
                'action' => $this->generateUrl('support:admin.add', [
                    'id' => $SupportEvent->getId(),
                    'message' => $message
                ]),
            ])
            ->handleRequest($request);


        if($form->isSubmitted() && $form->isValid() && $form->has('support_message_add'))
        {
            /** @note Не сбрасываем токен csrf формы для чата */

            $SupportDTO = new SupportDTO();
            $SupportEvent->getDto($SupportDTO);

            /** Меняем статус на "Закрытый" */
            $SupportDTO->setStatus(new SupportStatus(SupportStatusClose::class));

            /** Из ДТО входящего сообщения берем ДТО исходящего и добавляем с support */
            $SupportDTO->addMessage($ReplySupportMessageDto->getReply());

            $handle = $SupportHandler->handle($SupportDTO);

            if($request->isXmlHttpRequest() === false)
            {
                $this->addFlash(
                    'page.add',
                    $handle instanceof Support ? 'success.add' : 'danger.edit',
                    'support.admin',
                    $handle
                );
            }

            /** Скрываем тикет у остальных пользователей */
            $publish
                ->addData(['profile' => (string) $this->getCurrentProfileUid()])
                ->addData(['identifier' => (string) $SupportEvent->getMain()])
                ->send('remove');

            return $handle instanceof Support ? $this->redirectToRoute('support:admin.index') : $this->redirectToReferer();
        }

        return $this->render([
            'form' => $form->createView()
        ]);
    }
}