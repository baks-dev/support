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

namespace BaksDev\Support\UseCase\Admin\Add;

use BaksDev\Support\Answer\Repository\UserProfileTypeAnswers\UserProfileTypeAnswersInterface;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageForm;
use BaksDev\Users\Profile\UserProfile\Repository\UserProfileTokenStorage\UserProfileTokenStorageInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SupportMessageAddForm extends AbstractType
{
    public function __construct(
        private readonly UserProfileTypeAnswersInterface $UserProfileTypeAnswersRepository,
        private readonly Security $Security,
        private readonly UserProfileTokenStorageInterface $UserProfileTokenStorageRepository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event): void {

            /** @var SupportMessageAddDTO $SupportMessageAddDTO */
            $SupportMessageAddDTO = $event->getData();
            $builder = $event->getForm();


            /** Если пользователь не админ - указываем его профиль, чтобы отобразить только соответствующие ответы */
            if(false === $this->Security->isGranted('ROLE_ADMIN'))
            {
                $profile = $this->UserProfileTokenStorageRepository->getProfile();
                $this->UserProfileTypeAnswersRepository->forProfile($profile);
             }

            /** Список вариантов быстрых ответов на тикет */
            $UserProfileTypeAnswersResults = $this
                ->UserProfileTypeAnswersRepository
                ->forType($SupportMessageAddDTO->getTicketType())
                ->findAll();


            $builder->add(
                'reply',
                SupportMessageForm::class,
                [
                    'label' => false,
                    'disabled' => $SupportMessageAddDTO->getName() === 'system',
                    'supportAnswers' => $UserProfileTypeAnswersResults->valid() ? $UserProfileTypeAnswersResults : false,
                ],
            );


            if($SupportMessageAddDTO->isSubmit())
            {
                /* Сохранить ******************************************************/
                $builder->add(
                    'support_message_add',
                    SubmitType::class,
                    [
                        'label' => 'Отправить',
                        'label_html' => true,
                        'attr' => ['class' => 'btn-primary'],
                        'disabled' => $SupportMessageAddDTO->getName() === 'system',
                    ],
                );
            }

        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportMessageAddDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
            'supportAnswers' => [],
        ]);
    }
}