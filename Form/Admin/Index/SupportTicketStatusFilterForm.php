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

namespace BaksDev\Support\Form\Admin\Index;

use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatus\SupportStatusCollection;
use BaksDev\Support\Type\Status\SupportStatus\SupportStatusInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SupportTicketStatusFilterForm extends AbstractType
{
    private SessionInterface|false $session = false;

    private string $sessionKey;

    public function __construct(
        private readonly SupportStatusCollection $statuses,
        private readonly RequestStack $request,
    )
    {
        $this->sessionKey = md5(self::class);
    }

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


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event): void {
            /** @var SupportTicketStatusFilterDTO $data */
            $data = $event->getData();
            $builder = $event->getForm();

            if($this->session === false)
            {
                $this->session = $this->request->getSession();
            }

            if($this->session && $this->session->get('statusCode') === 307)
            {
                $this->session->remove($this->sessionKey);
                $this->session = false;
            }

            if($this->session && (time() - $this->session->getMetadataBag()->getLastUsed()) > 300)
            {
                $this->session->remove($this->sessionKey);
                $this->session = false;
            }

            if($this->session)
            {
                $sessionData = $this->request->getSession()->get($this->sessionKey);
                $sessionJson = $sessionData ? base64_decode($sessionData) : false;
                $sessionArray = $sessionJson !== false && json_validate($sessionJson) ? json_decode($sessionJson, true, 512, JSON_THROW_ON_ERROR) : false;

                if($sessionArray !== false)
                {
                    if(isset($sessionArray['status']))
                    {
                        /** @var SupportStatusInterface $declare */
                        foreach(SupportStatus::getDeclared() as $declare)
                        {
                            if($declare::equals($sessionArray['status']))
                            {
                                $data->setStatus(new $declare());
                                return;
                            }
                        }
                    }
                }
            }
        });


        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event): void {

                if($this->session === false)
                {
                    $this->session = $this->request->getSession();
                }

                if($this->session)
                {
                    /** @var SupportTicketStatusFilterDTO $data */
                    $data = $event->getData();

                    $sessionArray = [];

                    if($data->getStatus())
                    {
                        $sessionArray['status'] = (string) $data->getStatus()->getValue();
                    }

                    if($sessionArray)
                    {
                        $sessionJson = json_encode($sessionArray, JSON_THROW_ON_ERROR);
                        $sessionData = base64_encode($sessionJson);
                        $this->request->getSession()->set($this->sessionKey, $sessionData);
                        return;
                    }

                    $this->session->remove($this->sessionKey);

                }

            });
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