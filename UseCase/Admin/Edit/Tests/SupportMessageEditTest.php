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

namespace BaksDev\Support\UseCase\Admin\Edit\Tests;

use BaksDev\Support\Entity\Support;
use BaksDev\Support\Repository\SupportCurrentEvent\CurrentSupportEventInterface;
use BaksDev\Support\Type\Id\SupportUid;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;
use BaksDev\Support\UseCase\Admin\New\SupportHandler;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @group support
 *
 * @depends BaksDev\Support\UseCase\Admin\Add\Tests\SupportMessageAddTest::class
 */
#[When(env: 'test')]
class SupportMessageEditTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        // Бросаем событие консольной комманды
        $dispatcher = self::getContainer()->get(EventDispatcherInterface::class);
        $event = new ConsoleCommandEvent(new Command(), new StringInput(''), new NullOutput());
        $dispatcher->dispatch($event, 'console.command');


        /** @var CurrentSupportEventInterface $SupportCurrentEvent */
        $SupportCurrentEvent = self::getContainer()->get(CurrentSupportEventInterface::class);
        $SupportCurrentEvent->forSupport(SupportUid::TEST);
        $SupportEvent = $SupportCurrentEvent->execute();


        self::assertNotFalse($SupportEvent);
        self::assertNotNull($SupportEvent);


        /** @see SupportDTO */
        $SupportDTO = new SupportDTO();
        $SupportEvent->getDto($SupportDTO);


        /** SupportMessageDTO */

        /** @var SupportMessageDTO $SupportMessageDTO */
        $SupportMessageDTO = $SupportDTO->getMessages()->current();

        self::assertSame('Edit Support Message', $SupportMessageDTO->getMessage());
        $SupportMessageDTO->setMessage('Edit Add Message');
        self::assertSame('Edit Add Message', $SupportMessageDTO->getMessage());

        self::assertSame('Петр Иванов', $SupportMessageDTO->getName());
        $SupportMessageDTO->setName('Edit Add Message Name');
        self::assertSame('Edit Add Message Name', $SupportMessageDTO->getName());

        self::assertInstanceOf(DateTimeImmutable::class, $SupportMessageDTO->getDate());


        //        /** Меняем статус тикета на "Закрытый" */
        //        $SupportDTO->setStatus(new SupportStatus(SupportStatus\Collection\SupportStatusClose::PARAM));

        /** @var SupportHandler $SuportHandler */
        $SupportHandler = self::getContainer()->get(SupportHandler::class);
        $handle = $SupportHandler->handle($SupportDTO);

        self::assertTrue(($handle instanceof Support), $handle.': Ошибка Support');

    }
}
