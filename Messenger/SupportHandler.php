<?php

declare(strict_types=1);

namespace BaksDev\Support\Messenger;

use BaksDev\Support\Repository\AllMessagesByEvent\AllMessagesByEventInterface;
use BaksDev\Support\Repository\AllSupport\AllSupportInterface;
use BaksDev\Support\Repository\CurrentSupportMessage\CurrentSupportMessagesInterface;
use BaksDev\Support\Repository\SupportCurrentEvent\CurrentSupportEventInterface;
use BaksDev\Support\UseCase\Admin\Delete\SupportDeleteHandler;
use BaksDev\Support\UseCase\Admin\New\SupportHandler as SupportNewEditHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
final class SupportHandler
{
    public function __construct(
        private readonly SupportNewEditHandler $supportNeweditHandler,
        private readonly SupportDeleteHandler $supportDeleteHandler,
        private readonly CurrentSupportEventInterface $event,
        private readonly CurrentSupportMessagesInterface $supportMessage,
        private readonly AllSupportInterface $allSupportRepository,
        private readonly AllMessagesByEventInterface $allMessagesByTicketInterface,
    ) {}

    public function __invoke(SupportMessage $message): void {}
}
