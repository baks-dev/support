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

namespace BaksDev\Support\UseCase\Admin\Delete\Tests;

use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Repository\SupportCurrentEvent\CurrentSupportEventInterface;
use BaksDev\Support\Type\Id\SupportUid;
use BaksDev\Support\UseCase\Admin\Add\Tests\SupportMessageAddTest;
use BaksDev\Support\UseCase\Admin\Delete\SupportDeleteDTO;
use BaksDev\Support\UseCase\Admin\Delete\SupportDeleteHandler;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group support
 *
 * @depends BaksDev\Support\UseCase\Admin\Add\Tests\SupportMessageAddTest::class
 */
#[When(env: 'test')]
class SupportDeleteTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CurrentSupportEventInterface $SupportCurrentEvent */
        $SupportCurrentEvent = self::getContainer()->get(CurrentSupportEventInterface::class);
        $SupportCurrentEvent->forSupport(SupportUid::TEST);
        $SupportEvent = $SupportCurrentEvent->execute();

        self::assertNotNull($SupportEvent);
        self::assertNotFalse($SupportEvent);


        /** @see SupportDTO */
        $SupportDTO = new SupportDTO();
        $SupportEvent->getDto($SupportDTO);


        /** @see SupportDeleteDTO */
        $SupportDeleteDTO = new SupportDeleteDTO();
        $SupportEvent->getDto($SupportDeleteDTO);

        /** @var SupportDeleteHandler $SupportHandler */
        $SupportDeleteHandler = self::getContainer()->get(SupportDeleteHandler::class);
        $handle = $SupportDeleteHandler->handle($SupportDeleteDTO);

        self::assertTrue(($handle instanceof Support), $handle.': Ошибка Support');

    }

    public static function tearDownAfterClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $main = $em->getRepository(Support::class)
            ->findOneBy(['id' => SupportUid::TEST]);

        if($main)
        {
            $em->remove($main);
        }

        $event = $em->getRepository(SupportEvent::class)
            ->findBy(['main' => SupportUid::TEST]);

        foreach($event as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();
        $em->clear();
    }
}
