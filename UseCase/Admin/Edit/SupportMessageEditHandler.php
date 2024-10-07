<?php

declare(strict_types=1);

namespace BaksDev\Support\UseCase\Admin\Edit;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;

final class SupportMessageEditHandler extends AbstractHandler
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

        /** Не Сообщение не тправляем */

        return $this->main;
    }
}
