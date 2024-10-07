<?php


declare(strict_types=1);

namespace BaksDev\Support\Controller;

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
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

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
        #[ParamConverter(SupportMessageUid::class)] $message,
        #[MapEntity] SupportEvent $SupportEvent,
    ): Response {

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

        $SupportMessageDTO->setName($user['profile_username'] ?? null);

        /** Тема тикета */
        $ReplySupportMessageDto->setTitle($SupportEvent->getTitle());

        /** Сохраняеем в ДТО входящего сообщения ДТО исходящщего */
        $ReplySupportMessageDto->setReply($SupportMessageDTO);

        // Форма
        $form = $this->createForm(SupportMessageAddForm::class, $ReplySupportMessageDto, [
            'action' => $this->generateUrl('support:admin.add', [
                'id' => $SupportEvent->getId(),
                'message' => $message
            ]),
        ]);


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('support_message_add'))
        {

            $this->refreshTokenForm($form);


            $SupportDTO = new SupportDTO();
            $SupportEvent->getDto($SupportDTO);


            /** Меняем статус на "Закрытый" */
            $SupportDTO->setStatus(new SupportStatus(SupportStatusClose::PARAM));

            /** Из ДТО входящего сообщения берем ДТО исходящего и добавляем с support */
            $SupportDTO->addMessage($ReplySupportMessageDto->getReply());

            $handle = $SupportHandler->handle($SupportDTO);

            $this->addFlash(
                'page.add',
                $handle instanceof Support ? 'success.add' : 'danger.edit',
                'support.admin',
                $handle
            );

            return $handle instanceof Support ? $this->redirectToRoute('support:admin.index') : $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}
