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

namespace BaksDev\Support\UseCase\Admin\New\Tests;

use BaksDev\Support\Entity\Support;
use BaksDev\Support\Repository\SupportCurrentEvent\CurrentSupportEventInterface;
use BaksDev\Support\Type\Id\SupportUid;
use BaksDev\Support\Type\Priority\SupportPriority;
use BaksDev\Support\Type\Priority\SupportPriority\Collection\SupportPriorityHeight;
use BaksDev\Support\Type\Priority\SupportPriority\Collection\SupportPriorityLow;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatus\Collection\SupportStatusClose;
use BaksDev\Support\Type\Status\SupportStatus\Collection\SupportStatusOpen;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;
use BaksDev\Support\UseCase\Admin\New\SupportHandler;
use BaksDev\Users\Profile\TypeProfile\Type\Id\Choice\TypeProfileOrganization;
use BaksDev\Users\Profile\TypeProfile\Type\Id\Choice\TypeProfileUser;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
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
 * @depends BaksDev\Support\UseCase\Admin\New\Tests\SupportNewTest::class
 */
#[When(env: 'test')]
class SupportEditTest extends KernelTestCase
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


        self::assertTrue($SupportDTO->getPriority()->equals(SupportPriorityLow::PARAM));
        $SupportDTO->setPriority(new SupportPriority(SupportPriorityHeight::PARAM));
        self::assertTrue($SupportDTO->getPriority()->equals(SupportPriorityHeight::PARAM));

        self::assertTrue($SupportDTO->getStatus()->equals(SupportStatusOpen::PARAM));
        $SupportDTO->setStatus(new SupportStatus(SupportStatusClose::PARAM));
        self::assertTrue($SupportDTO->getStatus()->equals(SupportStatusClose::PARAM));

        /** SupportInvariableDTO */

        $SupportInvariableDTO = $SupportDTO->getInvariable();

        /** Присваиваем идентификатор профиля физ.лица TypeProfileUser::TYPE */
        self::assertTrue($SupportInvariableDTO->getType()->equals(TypeProfileOrganization::TYPE));
        $SupportInvariableDTO->setType(new TypeProfileUid(TypeProfileUser::TYPE));
        self::assertTrue($SupportInvariableDTO->getType()->equals(TypeProfileUser::TYPE));


        self::assertSame('00481e1e-cb75-4af7-b9ea-0d77dbad9914', $SupportInvariableDTO->getTicket());
        $SupportInvariableDTO->setTicket('84557148-cd8b-422e-bc93-dacd8aa6551e');
        self::assertSame('84557148-cd8b-422e-bc93-dacd8aa6551e', $SupportInvariableDTO->getTicket());

        self::assertSame('Triangle SnowLink PL02', $SupportInvariableDTO->getTitle());
        $SupportInvariableDTO->setTitle('Westlake ZuperAce SA-57');
        self::assertSame('Westlake ZuperAce SA-57', $SupportInvariableDTO->getTitle());


        /** SupportMessageDTO */

        /** @var  SupportMessageDTO $SupportMessageDTO */
        foreach ($SupportDTO->getMessages() as $SupportMessageDTO)
        {
            self::assertSame('Иван Петров', $SupportMessageDTO->getName());
            $SupportMessageDTO->setName('Петр Иванов');
            self::assertSame('Петр Иванов', $SupportMessageDTO->getName());

            self::assertSame('Добрый день! Хотел бы поинтересоваться у Вас по поводу комплекта свежих шин!', $SupportMessageDTO->getMessage());
            $SupportMessageDTO->setMessage('Edit Support Message');
            self::assertSame('Edit Support Message', $SupportMessageDTO->getMessage());

            self::assertInstanceOf(DateTimeImmutable::class, $SupportMessageDTO->getDate());
        }


        /** @var SupportHandler $SuportHandler */
        $SupportHandler = self::getContainer()->get(SupportHandler::class);
        $handle = $SupportHandler->handle($SupportDTO);

        self::assertTrue(($handle instanceof Support), $handle.': Ошибка Support');

    }
}
