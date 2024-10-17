<?php


declare(strict_types=1);

namespace BaksDev\Support\Controller;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\UseCase\Admin\Delete\SupportDeleteDTO;
use BaksDev\Support\UseCase\Admin\Delete\SupportDeleteForm;
use BaksDev\Support\UseCase\Admin\Delete\SupportDeleteHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_SUPPORT_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route('/admin/support/delete/{id}', name: 'admin.delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity] SupportEvent $SupportEvent,
        SupportDeleteHandler $SupportDeleteHandler,
    ): Response
    {

        $SupportDeleteDTO = new SupportDeleteDTO();
        $SupportEvent->getDto($SupportDeleteDTO);

        $form = $this->createForm(SupportDeleteForm::class, $SupportDeleteDTO, [
            'action' => $this->generateUrl('support:admin.delete', ['id' => $SupportDeleteDTO->getEvent()]),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('support_delete'))
        {
            $this->refreshTokenForm($form);

            $handle = $SupportDeleteHandler->handle($SupportDeleteDTO);

            $this->addFlash(
                'page.delete',
                $handle instanceof Support ? 'success.delete' : 'danger.delete',
                'support.admin',
                $handle
            );

            return $handle instanceof Support ? $this->redirectToRoute('support:admin.index', status: 400) : $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView(),]);
    }
}
