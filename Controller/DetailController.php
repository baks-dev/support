<?php


declare(strict_types=1);

namespace BaksDev\Support\Controller;

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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_SUPPORT_DETAIL')]
final class DetailController extends AbstractController
{
    #[Route('/admin/support/detail/{id}', name: 'admin.detail', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] SupportEvent $SupportEvent,
        CurrentUserProfileInterface $currentUserProfileDTO,
        AllMessagesByEventInterface $messagesByTicket,
    ): Response {
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

        /** Сохраняеем в ДТО входящего сообщения ДТО исходящщего */
        $ReplySupportMessageDto->setReply($SupportMessageDTO);

        // Форма
        $form = $this->createForm(SupportMessageAddForm::class, $ReplySupportMessageDto, [
            'action' => $this->generateUrl('support:admin.add', [
                'id' => $SupportEvent->getId(),
                'message' => current($messages)['message_id'] ?? null
            ]),
        ]);

        return $this->render([
            'messages' => $messages,
            'user' => $user['profile_username'] ?? null,
            'form' => $form->createView()
        ]);
    }
}
