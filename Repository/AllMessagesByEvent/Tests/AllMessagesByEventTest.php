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