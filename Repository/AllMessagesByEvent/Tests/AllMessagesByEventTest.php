<?php

namespace BaksDev\Support\Repository\AllMessagesByEvent\Tests;

use BaksDev\Support\Repository\AllMessagesByEvent\AllMessagesByEventInterface;
use BaksDev\Support\Type\Event\SupportEventUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group support
 */
class AllMessagesByEventTest extends KernelTestCase
{
    public function testUseCase(): void
    {

        /** @var AllMessagesByEventInterface $AllMessagesByTicketInterface */
        $AllMessagesByTicketInterface = self::getContainer()->get(AllMessagesByEventInterface::class);
        $AllMessagesByTicketInterface->forSupportEvent(SupportEventUid::TEST);

        $response = $AllMessagesByTicketInterface->findAll();

        if(!empty($response))
        {
            /** Если массив не пустой, то проверяем на наличие в нем ключей */
            self::assertTrue(array_key_exists("event", current($response)));
            self::assertTrue(array_key_exists("status", current($response)));
            self::assertTrue(array_key_exists("priority", current($response)));
            self::assertTrue(array_key_exists("ticket", current($response)));
            self::assertTrue(array_key_exists("title", current($response)));
            self::assertTrue(array_key_exists("message_id", current($response)));
            self::assertTrue(array_key_exists("name", current($response)));
            self::assertTrue(array_key_exists("message", current($response)));
            self::assertTrue(array_key_exists("date", current($response)));

        }

        self::assertTrue(true);
    }
}
