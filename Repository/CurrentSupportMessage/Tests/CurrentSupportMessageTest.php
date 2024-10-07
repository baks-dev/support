<?php

namespace BaksDev\Support\Repository\CurrentSupportMessage\Tests;

use BaksDev\Support\Entity\Message\SupportMessage;
use BaksDev\Support\Repository\CurrentSupportMessage\CurrentSupportMessagesInterface;
use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Message\SupportMessageUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group support
 *
 * @depends BaksDev\Support\Repository\AllSupport\Tests\AllSupportRepositoryTest::class
 */
class CurrentSupportMessageTest extends KernelTestCase
{
    public function testUseCase(): void
    {

        /** @var CurrentSupportMessagesInterface $CurrentSupportMessageInterface */
        $CurrentSupportMessageInterface = self::getContainer()->get(CurrentSupportMessagesInterface::class);

        $message = $CurrentSupportMessageInterface
            ->forEvent(SupportEventUid::TEST)
            ->forMessage(SupportMessageUid::TEST)
            ->find()
        ;

        self::assertInstanceOf(SupportMessage::class, $message);

        self::assertTrue(true);
    }
}
