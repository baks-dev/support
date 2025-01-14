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

namespace BaksDev\Support\Controller\Public;

use BaksDev\Support\Entity\Support;
use BaksDev\Support\Form\User\SupportFeedbackDTO;

use BaksDev\Support\Form\User\SupportFeedbackForm;
use BaksDev\Support\Type\Message\SupportMessageUid;
use BaksDev\Support\Type\Priority\SupportPriority;
use BaksDev\Support\Type\Priority\SupportPriority\Collection\SupportPriorityLow;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatus\Collection\SupportStatusOpen;
use BaksDev\Support\UseCase\Admin\New\Invariable\SupportInvariableDTO;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;
use BaksDev\Users\Profile\TypeProfile\Type\Id\Choice\TypeProfileUser;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BaksDev\Core\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

use BaksDev\Support\UseCase\User\Feedback\SupportFeedbackHandler;

#[AsController]
final class FeedbackController extends AbstractController
{
    public const string FORM_TITLE = 'Свяжитесь с нами';

    #[Route('/feedback', name: 'public.feedback', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        SupportFeedbackHandler $handler,
    ): Response
    {
        $currentUser = $this->getUsr();

        /** Форма обратной связи */
        $feedback = new SupportFeedbackDTO();
        $feedbackForm = $this->createForm(SupportFeedbackForm::class, $feedback);

        $feedbackForm->handleRequest($request);

        if($feedbackForm->isSubmitted() && $feedbackForm->isValid())
        {
            $this->refreshTokenForm($feedbackForm);

            $SupportDTO = new SupportDTO();
            $SupportDTO->setStatus(new SupportStatus(SupportStatusOpen::PARAM));

            /**
             * Задаем низкий приоритет
             */
            $SupportDTO->setPriority(new SupportPriority(SupportPriorityLow::PARAM));

            /** SupportInvariableDTO */
            $SupportInvariableDTO = new SupportInvariableDTO();

            /**
             * Для авторизованного задать UserProfileUid
             *
             * @var UserInterface $currentUser
             */
            if($currentUser !== null)
            {
                $SupportInvariableDTO->setProfile(new UserProfileUid($currentUser->getProfile()));
            }

            /**
             * Присваиваем идентификатор профиля
             */
            $SupportInvariableDTO->setType(new TypeProfileUid(TypeProfileUser::TYPE));

            $SupportInvariableDTO->setTicket(uniqid('', true));
            $SupportInvariableDTO->setTitle(self::FORM_TITLE);

            /**
             * Сообщение с данными
             */
            $SupportMessageDTO = new SupportMessageDTO();

            $SupportMessageDTO->setId(new SupportMessageUid());
            $SupportMessageDTO->setName($feedback->getName());

            $SupportMessageDTO->setMessage($feedback->getMessage());

            $feedbackDate = new DateTimeImmutable();
            $SupportMessageDTO->setDate($feedbackDate);

            $SupportDTO->addMessage($SupportMessageDTO);

            /**
             * Сообщение с номером телефона
             */
            $SupportMessageDTO = new SupportMessageDTO();
            //
            $SupportMessageDTO->setId(new SupportMessageUid());
            $SupportMessageDTO->setName($feedback->getName());

            $SupportMessageDTO->setMessage('Перезвонить по номеру: '.$feedback->getPhone());

            $feedbackDate = new DateTimeImmutable();
            $SupportMessageDTO->setDate($feedbackDate);

            $SupportDTO->addMessage($SupportMessageDTO);
            $SupportDTO->setInvariable($SupportInvariableDTO);

            $handle = $handler->handle($SupportDTO);

            $this->addFlash(
                'add.feedback',
                $handle instanceof Support ? 'add.success' : 'add.danger',
                'support.public',
                $handle
            );

            $this->redirectToReferer();
        }

        return $this->render(
            [
                'form' => $feedbackForm->createView(),
            ]
        );
    }
}
