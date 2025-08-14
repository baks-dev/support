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

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatus\Collection\SupportStatusClose;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;
use BaksDev\Support\UseCase\Admin\Status\SupportTicketStatusDTO;
use BaksDev\Support\UseCase\Admin\Status\SupportTicketStatusForm;
use BaksDev\Support\UseCase\Admin\Status\SupportTicketStatusHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_SUPPORT_CLOSED')]
final class ClosedController extends AbstractController
{
    #[Route('/admin/support/closed/{id}', name: 'admin.closed', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity] SupportEvent $SupportEvent,
        SupportTicketStatusHandler $SupportDeleteHandler,
    ): Response
    {

        $SupportDTO = $SupportEvent->getDto(SupportDTO::class);

        $SupportTicketClosedDTO = new SupportTicketStatusDTO();

        $form = $this
            ->createForm(
                type: SupportTicketStatusForm::class,
                data: $SupportTicketClosedDTO,
                options: ['action' => $this->generateUrl('support:admin.closed', ['id' => $SupportEvent->getId()])],
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('support_ticket_closed'))
        {
            $this->refreshTokenForm($form);

            $SupportDTO->setStatus(new SupportStatus(SupportStatusClose::PARAM));

            $handle = $SupportDeleteHandler->handle($SupportDTO);

            $this->addFlash(
                'page.closed',
                $handle instanceof Support ? 'success.closed' : 'danger.closed',
                'support.admin',
                $handle,
            );

            return $handle instanceof Support ? $this->redirectToRoute('support:admin.index') : $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView(),]);
    }
}