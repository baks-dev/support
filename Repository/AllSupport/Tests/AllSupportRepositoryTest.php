<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
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
use BaksDev\Support\Repository\AllMessagesByEvent\Tests\AllMessagesByEventTest;
use BaksDev\Support\Repository\AllSupport\AllSupportInterface;
use BaksDev\Support\Repository\AllSupport\AllSupportResult;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('support')]
class AllSupportRepositoryTest extends KernelTestCase
{
    #[DependsOnClass(AllMessagesByEventTest::class)]
    public function testUseCase(): void
    {
        self::assertTrue(true);

        /** @var AllSupportInterface $AllSupportRepositoryInterface */
        $AllSupportRepositoryInterface = self::getContainer()->get(AllSupportInterface::class);

        $paginator = $AllSupportRepositoryInterface
            ->search(new SearchDTO())
            ->profile(new UserProfileUid())
            ->findPaginator();

        if(empty($paginator->getData()))
        {
            return;
        }

        foreach($paginator->getData() as $AllSupportResult)
        {
            // Вызываем все геттеры
            $reflectionClass = new ReflectionClass(AllSupportResult::class);
            $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach($methods as $method)
            {
                // Методы без аргументов
                if($method->getNumberOfParameters() === 0)
                {
                    // Вызываем метод
                    $data = $method->invoke($AllSupportResult);
                    // dump($data);
                }
            }

        }

    }
}