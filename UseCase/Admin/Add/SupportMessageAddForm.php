<?php

declare(strict_types=1);

namespace BaksDev\Support\UseCase\Admin\Add;

use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SupportMessageAddForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'reply',
            SupportMessageForm::class,
            ['label' => false]
        );

        /* Сохранить ******************************************************/
        $builder->add(
            'support_message_add',
            SubmitType::class,
            ['label' => 'Отправить', 'label_html' => true, 'attr' => ['class' => 'btn-primary']]
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportMessageAddDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
        ]);
    }
}
