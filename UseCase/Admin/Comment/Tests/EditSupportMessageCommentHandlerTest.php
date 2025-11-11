<?php
/*
 * Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Support\UseCase\Admin\Comment\Tests;

use BaksDev\Support\Entity\Support;
use BaksDev\Support\Repository\SupportCurrentEvent\CurrentSupportEventInterface;
use BaksDev\Support\Type\Id\SupportUid;
use BaksDev\Support\UseCase\Admin\Comment\EditSupportMessageCommentDTO;
use BaksDev\Support\UseCase\Admin\Comment\EditSupportMessageCommentHandler;
use BaksDev\Support\UseCase\Admin\New\Tests\SupportNewTest;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[Group('support')]
#[Group('support-usecase')]
#[When(env: 'test')]
final class EditSupportMessageCommentHandlerTest extends KernelTestCase
{
    #[DependsOnClass(SupportNewTest::class)]
    public function testUseCase(): void
    {
        /** @var CurrentSupportEventInterface $SupportCurrentEventRepository */
        $SupportCurrentEventRepository = self::getContainer()->get(CurrentSupportEventInterface::class);
        $supportMessageCommentEvent = $SupportCurrentEventRepository
            ->forSupport(SupportUid::TEST)
            ->find();

        $supportMessageCommentDTO = new EditSupportMessageCommentDTO();
        $supportMessageCommentEvent->getDto($supportMessageCommentDTO);

        $supportMessageCommentDTO->setComment('test comment');

        /** @var EditSupportMessageCommentHandler $EditSupportMessageCommentHandler */
        $SupportMessageHandler = self::getContainer()->get(EditSupportMessageCommentHandler::class);
        $handle = $SupportMessageHandler->handle($supportMessageCommentDTO);

        self::assertTrue(($handle instanceof Support), $handle.': Ошибка Support');


        /** Проверка изменения значения комментария */
        $supportMessageCommentEvent = $SupportCurrentEventRepository
            ->forSupport(SupportUid::TEST)
            ->find();
        self::assertEquals('test comment', $supportMessageCommentEvent->getComment());
    }
}