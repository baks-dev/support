<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

        $form = $this
            ->createForm(SupportDeleteForm::class, $SupportDeleteDTO, [
                'action' => $this->generateUrl(
                    'support:admin.delete',
                    ['id' => $SupportDeleteDTO->getEvent()]
                ),
            ])
            ->handleRequest($request);


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
