<?php

declare(strict_types=1);

namespace BaksDev\Support\UseCase\Admin\New;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Messenger\SupportMessage;

final class SupportHandler extends AbstractHandler
{
    /** @see Support */
    public function handle(SupportDTO $command): string|Support
    {

        $this->setCommand($command);
        $this->preEventPersistOrUpdate(Support::class, SupportEvent::class);

        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }

        $this->flush();

        /** Отправляем сообщение в шину */
        $this->messageDispatch->dispatch(
            message: new SupportMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
            transport: 'support'
        );

        return $this->main;
    }
}
