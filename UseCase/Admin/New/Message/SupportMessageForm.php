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

namespace BaksDev\Support\UseCase\Admin\New\Message;

use BaksDev\Auth\Telegram\Type\Status\AccountTelegramStatus;
use BaksDev\Support\Answer\Entity\SupportAnswer;
use BaksDev\Support\Answer\Repository\UserProfileTypeAnswers\UserProfileTypeAnswersResult;
use Generator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SupportMessageForm extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('message', TextareaType::class, ['label' => false, 'required' => true]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event): void {

            /** @var SupportMessageDTO $data */
            $SupportMessageDTO = $event->getData();
            $form = $event->getForm();

            if($SupportMessageDTO->getName() === null)
            {
                $form->add('name', TextType::class);
            }

        });

        /**
         * Варианты ответов
         */

        /** @var Generator<int, UserProfileTypeAnswersResult> $supportAnswers */
        $supportAnswers = $options['supportAnswers'];

        $builder->add('answers', ChoiceType::class, [
            'choices' => $supportAnswers, // Ответы для типа профиля

            'choice_value' => function(?UserProfileTypeAnswersResult $answer) {
                return $answer?->getId();
            },

            'choice_label' => function(UserProfileTypeAnswersResult $answer) {
                return $answer->getTitle();
            },

            'choice_attr' => function(?UserProfileTypeAnswersResult $choice) {
                return ['data-content' => $choice?->getContent()];
            },

            'required' => false,
            'expanded' => false,
            'multiple' => false,
            'label' => false,
            'disabled' => (empty($supportAnswers) || $supportAnswers->valid() === false),
            'attr' => [
                'title' => (empty($supportAnswers) || $supportAnswers->valid() === false) ?
                    $this->translator->trans('answers.title.no_answers', domain: 'support-answer.admin') :
                    $this->translator->trans('answers.title.has_answers', domain: 'support-answer.admin'),
            ],

            'translation_domain' => 'support-answer.admin',
        ]);

        $builder->get('answers')->resetViewTransformers();

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportMessageDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
            'supportAnswers' => [],
        ]);
    }
}