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

namespace BaksDev\Support\Controller\Public;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Type\Priority\SupportPriority;
use BaksDev\Support\Type\Priority\SupportPriority\Collection\SupportPriorityLow;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatus\Collection\SupportStatusOpen;
use BaksDev\Support\UseCase\Admin\New\Invariable\SupportInvariableDTO;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;
use BaksDev\Support\UseCase\Public\Feedback\SupportFeedbackDTO;
use BaksDev\Support\UseCase\Public\Feedback\SupportFeedbackForm;
use BaksDev\Support\UseCase\Public\Feedback\SupportFeedbackHandler;
use BaksDev\Users\Profile\TypeProfile\Type\Id\Choice\TypeProfileUser;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
final class FeedbackController extends AbstractController
{
    #[Route('/feedback', name: 'public.feedback', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        TranslatorInterface $translator,
        SupportFeedbackHandler $handler,
    ): Response
    {

        /** Форма обратной связи */
        $feedback = new SupportFeedbackDTO();
        $feedbackForm = $this
            ->createForm(
                SupportFeedbackForm::class, $feedback,
                ['action' => $this->generateUrl('support:public.feedback')]
            )
            ->handleRequest($request);

        if($feedbackForm->isSubmitted() && $feedbackForm->isValid())
        {
            $this->refreshTokenForm($feedbackForm);

            /** SupportInvariableDTO */

            $SupportInvariableDTO = new SupportInvariableDTO();
            $SupportInvariableDTO
                ->setProfile($this->getProfileUid())
                ->setType(new TypeProfileUid(TypeProfileUser::TYPE))
                ->setTicket(uniqid('', true))
                ->setTitle($translator->trans('add.title', domain: 'support.public'));

            /** SupportDTO */

            $SupportDTO = new SupportDTO();
            $SupportDTO
                ->setStatus(new SupportStatus(SupportStatusOpen::PARAM))
                ->setPriority(new SupportPriority(SupportPriorityLow::PARAM))
                ->setInvariable($SupportInvariableDTO);

            /**
             * Сообщение с данными
             */
            $SupportMessageDataDTO = new SupportMessageDTO();
            $SupportMessageDataDTO
                ->setName($feedback->getName())
                ->setMessage($feedback->getMessage());

            /**
             * Сообщение с номером телефона
             */
            $SupportMessagePhoneDTO = new SupportMessageDTO();
            $SupportMessagePhoneDTO
                ->setName($feedback->getName())
                ->setMessage(
                    $translator->trans(
                        id: 'add.message',
                        parameters: ['phone' => $feedback->getPhone()],
                        domain: 'support.public'
                    )
                );

            $SupportDTO->addMessage($SupportMessageDataDTO);
            $SupportDTO->addMessage($SupportMessagePhoneDTO);

            $handle = $handler->handle($SupportDTO);

            $this->addFlash(
                'add.feedback',
                $handle instanceof Support ? 'add.success' : 'add.danger',
                'support.public',
                $handle
            );

            return $this->redirectToReferer();
        }

        return $this->render(['form' => $feedbackForm->createView(),]);
    }
}
