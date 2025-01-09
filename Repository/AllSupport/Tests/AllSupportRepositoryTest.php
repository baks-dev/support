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

namespace BaksDev\Support\Repository\AllSupport\Tests;

use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Support\Repository\AllSupport\AllSupportInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group support
 *
 * @depends BaksDev\Support\Repository\AllMessagesByEvent\Tests\AllMessagesByEventTest::class
 */
class AllSupportRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {

        /** @var AllSupportInterface $AllSupportRepositoryInterface */
        $AllSupportRepositoryInterface = self::getContainer()->get(AllSupportInterface::class);

        $response = $AllSupportRepositoryInterface
            ->search(new SearchDTO())
            ->findPaginator();

        if(!empty($response->getData()))
        {
            $current = current($response->getData());

            self::assertTrue(array_key_exists("id", $current));
            self::assertTrue(array_key_exists("event", $current));
            self::assertTrue(array_key_exists("status", $current));
            self::assertTrue(array_key_exists("priority", $current));
            self::assertTrue(array_key_exists("ticket", $current));
            self::assertTrue(array_key_exists("title", $current));
            self::assertTrue(array_key_exists("name", $current));
            self::assertTrue(array_key_exists("message", $current));
            self::assertTrue(array_key_exists("message_id", $current));
        }

        self::assertTrue(true);
    }
}