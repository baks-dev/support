<?php

namespace BaksDev\Support\Repository\AllSupport\Tests;

use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Support\Repository\AllSupport\AllSupportRepositoryInterface;
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
        /** @var AllSupportRepositoryInterface $AllSupportRepositoryInterface */
        $AllSupportRepositoryInterface = self::getContainer()->get(AllSupportRepositoryInterface::class);

        $response = $AllSupportRepositoryInterface
            ->search(new SearchDTO())
            ->findPaginator();

        if(!empty($response->getData()))
        {

            self::assertTrue(array_key_exists("id", current($response->getData())));
            self::assertTrue(array_key_exists("event", current($response->getData())));
            self::assertTrue(array_key_exists("status", current($response->getData())));
            self::assertTrue(array_key_exists("priority", current($response->getData())));
            self::assertTrue(array_key_exists("ticket", current($response->getData())));
            self::assertTrue(array_key_exists("title", current($response->getData())));
            self::assertTrue(array_key_exists("name", current($response->getData())));
            self::assertTrue(array_key_exists("message", current($response->getData())));
            self::assertTrue(array_key_exists("message_id", current($response->getData())));
        }


        self::assertTrue(true);
    }
}
