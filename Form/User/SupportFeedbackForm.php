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

namespace BaksDev\Support\Form\User;

use BaksDev\Captcha\Security\CaptchaVerifyInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvents;

final class SupportFeedbackForm extends AbstractType
{

    public function __construct(
        private readonly Security $security,
        private readonly CaptchaVerifyInterface $captchaVerify
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'required' => true,
            'attr' => [
                'placeholder' => 'Ваше имя',
                'aria-label' => 'Поле для ввода имени',
            ],

        ])
            ->add('phone', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ваш телефон',
                    'aria-label' => 'Поле для ввода телефона',
                ],
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Сообщение',
                    'aria-label' => 'Поле для ввода сообщения',
                ],
            ]);

        /**
         * Капча
         */
        $currentUser = $this->security->getUser();
        if($currentUser === null)
        {
            $builder->add('code', TextType::class);

            $builder->get('code')->addEventListener(
                FormEvents::POST_SUBMIT,
                function(FormEvent $event): void {

                    $code = $event->getForm()->getData();
                    $verify = $this->captchaVerify->verify($code);

                    /** @var SupportFeedbackDTO $SupportFeedbackDTO */
                    $SupportFeedbackDTO = $event->getForm()->getParent()?->getData();

                    if($verify)
                    {
                        $SupportFeedbackDTO->captchaValid();
                    }
                }
            );
        }

        /* Сохранить ******************************************************/
        $builder->add(
            'support_feedback',
            SubmitType::class,
            ['label' => 'Отправить', 'label_html' => true, 'attr' => ['class' => 'btn btn-dark']]
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportFeedbackDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'bg-light d-flex row p-4 rounded-4 form-shadow feedback-form'],
        ]);

        /**
         * Задать группу 'anonymous_user' validation_groups для валидации полей 'captcha' и 'code' SupportFeedbackDTO
         */
        $resolver->setDefaults([
            'validation_groups' => function(FormInterface $form): array {
                $currentUser = $this->security->getUser();
                return $currentUser === null ? ['Default', 'anonymous_user'] : ['Default'];
            },
        ]);
    }
}