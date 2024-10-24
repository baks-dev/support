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

namespace BaksDev\Support\Repository\FindExistTicket\Tests;

use BaksDev\Support\Repository\FindExistTicket\FindExistTicketInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * group support
 */
class FindExistTicketTest extends KernelTestCase
{
    public function testUseCase(): void
    {

        /** @var FindExistTicketInterface $AFindTicketByIdInterface */
        $AFindTicketByIdInterface = self::getContainer()->get(FindExistTicketInterface::class);
        $AFindTicketByIdInterface->forTicket('00481e1e-cb75-4af7-b9ea-0d77dbad9914');


        $response = $AFindTicketByIdInterface->exist();
                dd($response);


        self::assertTrue(true);
    }
}