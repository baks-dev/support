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

namespace BaksDev\Support\UseCase\Admin\Add\Tests;

use BaksDev\Support\Entity\Support;
use BaksDev\Support\Repository\SupportCurrentEvent\CurrentSupportEventInterface;
use BaksDev\Support\Type\Id\SupportUid;
use BaksDev\Support\Type\Status\SupportStatus;
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
 * @depends BaksDev\Support\UseCase\Admin\New\Tests\SupportEditTest::class
 */
#[When(env: 'test')]
class SupportMessageAddTest extends KernelTestCase
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
        $SupportEvent = $SupportCurrentEvent->find();


        self::assertNotFalse($SupportEvent);
        self::assertNotNull($SupportEvent);


        /** @see SupportDTO */
        $SupportDTO = new SupportDTO();
        $SupportEvent->getDto($SupportDTO);


        /** First SupportMessageDTO */

        $SupportMessageDTO = new SupportMessageDTO();

        $SupportMessageDTO->setName('Add Test Name (first)');
        self::assertSame('Add Test Name (first)', $SupportMessageDTO->getName());

        $SupportMessageDTO->setMessage('Add Test Message (first)');
        self::assertSame('Add Test Message (first)', $SupportMessageDTO->getMessage());

        $SupportMessageDTO->setDate(new DateTimeImmutable('10 minutes ago'));

        $SupportMessageDTO->setOutMessage();
        self::assertTrue($SupportMessageDTO->getOut());

        $SupportDTO->addMessage($SupportMessageDTO);

        /** Меняем статус тикета на "Открытый" */
        $SupportDTO->setStatus(new SupportStatus(SupportStatus\Collection\SupportStatusOpen::PARAM));


        /** Second SupportMessageDTO */

        $SupportMessageDTO = new SupportMessageDTO();

        $SupportMessageDTO->setName('Add Test Name (second)');
        self::assertSame('Add Test Name (second)', $SupportMessageDTO->getName());

        $SupportMessageDTO->setMessage('Add Test Message (second)');
        self::assertSame('Add Test Message (second)', $SupportMessageDTO->getMessage());


        $SupportMessageDTO->setDate(new DateTimeImmutable('5 minutes ago'));

        $SupportMessageDTO->setInMessage();
        self::assertFalse($SupportMessageDTO->getOut());


        $SupportDTO->addMessage($SupportMessageDTO);

        /** Меняем статус тикета на "Открытый" */
        $SupportDTO->setStatus(new SupportStatus(SupportStatus\Collection\SupportStatusOpen::PARAM));


        /** @var SupportHandler $SuportHandler */
        $SupportHandler = self::getContainer()->get(SupportHandler::class);
        $handle = $SupportHandler->handle($SupportDTO);

        self::assertTrue(($handle instanceof Support), $handle.': Ошибка Support');

    }
}
