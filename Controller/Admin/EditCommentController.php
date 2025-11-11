<?php
/*
 * Copyright 2025.  Baks.dev <admin@baks.dev>
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
use BaksDev\Support\UseCase\Admin\Comment\EditSupportMessageCommentDTO;
use BaksDev\Support\UseCase\Admin\Comment\EditSupportMessageCommentForm;
use BaksDev\Support\UseCase\Admin\Comment\EditSupportMessageCommentHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_SUPPORT_COMMENT')]
final class EditCommentController extends AbstractController
{
    #[Route('/admin/support/comment/edit/{id}', name: 'admin.comment.newedit', methods: ['POST'])]
    public function edit(
        #[MapEntity] SupportEvent $supportCommentEvent,
        Request $request,
        EditSupportMessageCommentHandler $SupportCommentHandler,
    ): Response
    {
        $supportCommentDTO = new EditSupportMessageCommentDTO();
        $supportCommentEvent->getDto($supportCommentDTO);


        // Форма
        $form = $this
            ->createForm(
                EditSupportMessageCommentForm::class,
                $supportCommentDTO,
                ['action' => $this->generateUrl(
                    'support:admin.comment.newedit',
                    ['id' => $supportCommentDTO->getEvent()]
                )]
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('edit_support_message_comment'))
        {
            $handle = $SupportCommentHandler->handle($supportCommentDTO);

            $this->addFlash(
                'page.comment',
                $handle instanceof Support ? 'success.comment' : 'danger.comment',
                'support.admin',
                $handle
            );

            return $this->redirectToReferer();
        }

        $this->addFlash('page.comment', 'danger.comment', 'support.admin');

        return $this->redirectToReferer();
    }
}