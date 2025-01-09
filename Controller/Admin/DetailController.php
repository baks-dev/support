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
use BaksDev\Centrifugo\Services\Token\TokenUserGenerator;
use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Repository\AllMessagesByEvent\AllMessagesByEventInterface;
use BaksDev\Support\UseCase\Admin\Add\SupportMessageAddDTO;
use BaksDev\Support\UseCase\Admin\Add\SupportMessageAddForm;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;
use BaksDev\Users\Profile\UserProfile\Repository\CurrentUserProfile\CurrentUserProfileInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_SUPPORT_DETAIL')]
final class DetailController extends AbstractController
{
    #[Route('/admin/support/detail/{id}', name: 'admin.detail', methods: ['GET', 'POST'])]
    public function edit(
        #[MapEntity] SupportEvent $SupportEvent,
        CurrentUserProfileInterface $currentUserProfileDTO,
        AllMessagesByEventInterface $messagesByTicket,
        CentrifugoPublishInterface $publish,
        TokenUserGenerator $tokenUserGenerator,
    ): Response
    {
        if(is_null($SupportEvent->getTitle()))
        {
            return $this->redirectToRoute('support:admin.index');
        }

        /** Скрываем тикет у остальных пользователей */
        $publish
            ->addData(['profile' => (string) $this->getProfileUid()])
            ->addData(['identifier' => (string) $SupportEvent->getMain()])
            ->send('remove');

        $user = $currentUserProfileDTO->fetchProfileAssociative($this->getCurrentUsr());

        $SupportDTO = new SupportDTO();
        $SupportEvent->getDto($SupportDTO);

        $messages = $messagesByTicket
            ->forSupportEvent($SupportEvent->getId())
            ->findAll();

        /** Входящее сообщение */
        $ReplySupportMessageDto = new SupportMessageAddDTO();

        /** Исходящее сообщение */
        $SupportMessageDTO = new SupportMessageDTO();

        $SupportMessageDTO->setName($user['profile_username'] ?? null);

        /** Тема тикета */
        $ReplySupportMessageDto->setTitle($SupportEvent->getTitle());

        /** Сохраняем в ДТО входящего сообщения ДТО исходящего */
        $ReplySupportMessageDto->setReply($SupportMessageDTO);

        // Форма
        $form = $this->createForm(SupportMessageAddForm::class, $ReplySupportMessageDto, [
            'action' => $this->generateUrl('support:admin.add', [
                'id' => $SupportEvent->getId(),
                'message' => current($messages)['message_id'] ?? null
            ]),
        ]);

        return $this->render([
            'identifier' => $SupportEvent->getMain(),
            'messages' => $messages,
            'user' => $user['profile_username'] ?? null,
            'form' => $form->createView(),
            'token' => $tokenUserGenerator->generate($this->getUsr()),
        ]);
    }
}