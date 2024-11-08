<?php

declare(strict_types=1);

namespace BaksDev\Support\Form\Admin\Index;

use BaksDev\Support\Type\Status\SupportStatus\SupportStatusCollection;
use BaksDev\Support\Type\Status\SupportStatus\SupportStatusInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SupportTicketStatusFilterForm extends AbstractType
{
    public function __construct(private SupportStatusCollection $statuses,) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add('status', ChoiceType::class, [
            'choices' => $this->statuses->cases(),
            'choice_value' => function(?SupportStatusInterface $status) {
                return $status !== null ? $status::class : null;
            },
            'choice_label' => function(SupportStatusInterface $status) {
                return 'filter.'.$status->getValue();
            },
            'label' => false,
            'translation_domain' => 'support.admin'
        ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportTicketStatusFilterDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
        ]);
    }
}