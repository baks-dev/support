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

namespace BaksDev\Support\Twig;

use BaksDev\Centrifugo\Services\Token\TokenUserGenerator;
use BaksDev\Core\Twig\TemplateExtension;
use BaksDev\Users\User\Entity\User;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SupportWidgetExtension extends AbstractExtension
{
    public function __construct(
        private readonly TemplateExtension $template,
        private readonly TokenUserGenerator $tokenUserGenerator,
        private readonly TokenStorageInterface $tokenStorage
    ) {}
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_support_widget',
                [$this, 'renderSupportWidget'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    protected function getUsr(): UserInterface|User|null
    {
        try
        {
            $token = $this->tokenStorage->getToken();
        }
        catch(UnauthorizedHttpException $exception)
        {
            $token = null;
        }

        return $token?->getUser();
    }

    public function renderSupportWidget(Environment $twig): string
    {

        $path = $this->template->extends('@support-widget:support.widget.html.twig');

        return $twig->render
        (
            name: $path,
            context: [
                'token' => $this->tokenUserGenerator->generate($this->getUsr())
            ]
        );
    }
}