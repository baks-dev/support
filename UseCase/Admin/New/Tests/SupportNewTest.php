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

namespace BaksDev\Support\UseCase\Admin\New\Tests;

use BaksDev\Support\Entity\Event\SupportEvent;
use BaksDev\Support\Entity\Support;
use BaksDev\Support\Type\Id\SupportUid;
use BaksDev\Support\Type\Message\SupportMessageUid;
use BaksDev\Support\Type\Priority\SupportPriority;
use BaksDev\Support\Type\Priority\SupportPriority\Collection\SupportPriorityLow;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\Type\Status\SupportStatus\Collection\SupportStatusOpen;
use BaksDev\Support\UseCase\Admin\New\Invariable\SupportInvariableDTO;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use BaksDev\Support\UseCase\Admin\New\SupportDTO;
use BaksDev\Support\UseCase\Admin\New\SupportHandler;
use BaksDev\Users\Profile\TypeProfile\Type\Id\Choice\TypeProfileOrganization;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[When(env: 'test')]
#[Group('support')]
class SupportNewTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {

        // Бросаем событие консольной комманды
        $dispatcher = self::getContainer()->get(EventDispatcherInterface::class);
        $event = new ConsoleCommandEvent(new Command(), new StringInput(''), new NullOutput());
        $dispatcher->dispatch($event, 'console.command');

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


    public function testUseCase(): void
    {

        /** SupportDTO */
        $SupportDTO = new SupportDTO(); // done

        /** Присваиваем токен для последующего поиска */
        $SupportDTO->getToken()->setValue(new UserProfileUid(UserProfileUid::TEST));

        $SupportDTO->setPriority(new SupportPriority(SupportPriorityLow::PARAM));
        self::assertTrue($SupportDTO->getPriority()->equals(SupportPriorityLow::PARAM));

        $SupportDTO->setStatus(new SupportStatus(SupportStatusOpen::PARAM));
        self::assertTrue($SupportDTO->getStatus()->equals(SupportStatusOpen::PARAM));

        /** SupportInvariableDTO */
        $SupportInvariableDTO = new SupportInvariableDTO();

        $SupportInvariableDTO->setProfile(new UserProfileUid(UserProfileUid::TEST));
        self::assertTrue($SupportInvariableDTO->getProfile()->equals(UserProfileUid::TEST));

        /** Присваиваем идентификатор профиля юр.лица TypeProfileOrganization::TYPE  */
        $SupportInvariableDTO->setType(new TypeProfileUid(TypeProfileOrganization::TYPE));
        self::assertTrue($SupportInvariableDTO->getType()->equals(TypeProfileOrganization::TYPE));


        $SupportInvariableDTO->setTicket('b7d886ab-8fdd-7275-9dba-bf7e1327e885');
        self::assertSame('b7d886ab-8fdd-7275-9dba-bf7e1327e885', $SupportInvariableDTO->getTicket());

        $SupportInvariableDTO->setTitle('New Test Title');
        self::assertSame('New Test Title', $SupportInvariableDTO->getTitle());


        /** SupportMessageDTO */
        $SupportMessageDTO = new SupportMessageDTO();

        $SupportMessageDTO->setId(new SupportMessageUid(SupportMessageUid::TEST));

        $SupportMessageDTO->setExternal('c5bddd02');
        self::assertSame('c5bddd02', $SupportMessageDTO->getExternal());

        $SupportMessageDTO->setName('New Test Name');
        self::assertSame('New Test Name', $SupportMessageDTO->getName());

        $SupportMessageDTO->setMessage('New Test Message');
        self::assertSame('New Test Message', $SupportMessageDTO->getMessage());

        $testDate = new DateTimeImmutable('2024-10-29');
        $SupportMessageDTO->setDate($testDate);
        self::assertSame($testDate, $SupportMessageDTO->getDate());

        $SupportMessageDTO->setOutMessage();
        self::assertTrue($SupportMessageDTO->getOut());

        $SupportDTO->addMessage($SupportMessageDTO);


        $SupportDTO->setInvariable($SupportInvariableDTO);
        self::assertSame($SupportInvariableDTO, $SupportDTO->getInvariable());


        /** @var SupportHandler $SupportHandler */
        $SupportHandler = self::getContainer()->get(SupportHandler::class);
        $handle = $SupportHandler->handle($SupportDTO);

        self::assertTrue(($handle instanceof Support), $handle.': Ошибка Support');

    }
}