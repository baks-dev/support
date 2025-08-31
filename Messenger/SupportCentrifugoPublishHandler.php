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

namespace BaksDev\Support\Messenger;

use BaksDev\Centrifugo\Server\Publish\CentrifugoPublishInterface;
use BaksDev\Core\Twig\TemplateExtension;
use BaksDev\Support\Repository\SupportLastMessage\SupportLastMessageInterface;
use BaksDev\Support\Repository\SupportLastMessage\SupportLastMessageResult;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Environment;

/** Отправляет идентификаторы сообщения в Centrifugo */
#[AsMessageHandler(priority: 0)]
final readonly class SupportCentrifugoPublishHandler
{
    public function __construct(
        private SupportLastMessageInterface $supportLastMessage,
        private Environment $environment,
        private TemplateExtension $templateExtension,
        private ?CentrifugoPublishInterface $CentrifugoPublish = null,
    ) {}

    public function __invoke(SupportMessage $message): void
    {
        if(false === ($this->CentrifugoPublish instanceof CentrifugoPublishInterface))
        {
            return;
        }

        /** @var SupportLastMessageResult $SupportMessage */
        $SupportMessage = $this->supportLastMessage
            ->forSupport($message->getId())
            ->find();

        if(false === ($SupportMessage instanceof SupportLastMessageResult))
        {
            return;
        }

        $template = $this->templateExtension->extends('@support:admin/detail/pc/message.html.twig');
        $render = $this->environment->render($template, ['message' => $SupportMessage]);

        // Канал указать в зав-ти от наличия профиля
        $channel = ($SupportMessage->getProfile() instanceof UserProfileUid) ? $SupportMessage->getProfile().'_ticket' : 'ticket';

        /** Возвращаем идентификаторы для обновления формы c сообщением */
        $this->CentrifugoPublish
            ->addData(['identifier' => (string) $message->getId()])
            ->addData(['event' => (string) $message->getEvent()])
            ->addData(['message' => $SupportMessage->getMessageId()])
            ->addData(['support' => $render])
            // Добавить статус
            ->addData(['status' => (string) $message->getStatus()])
            ->send($channel);

    }
}