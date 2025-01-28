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

namespace BaksDev\Support\UseCase\Public\Feedback\Tests;

use BaksDev\Captcha\Security\CaptchaVerifyInterface;
use BaksDev\Support\UseCase\Public\Feedback\SupportFeedbackDTO;
use BaksDev\Support\UseCase\Public\Feedback\SupportFeedbackForm;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

/** @group support */
#[When(env: 'test')]
class SupportFeedbackFormTest extends TypeTestCase
{
    private readonly Security $security;
    private readonly CaptchaVerifyInterface $captchaVerify;

    protected function setUp(): void
    {
        /**
         * Мокируем зависимости
         */
        $this->security = $this->createMock(Security::class);
        $this->captchaVerify = $this->createMock(CaptchaVerifyInterface::class);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        /**
         * Создание экземпляра формы с мокированными зависимости
         */
        $type = new SupportFeedbackForm($this->security, $this->captchaVerify);

        return [
            /**
             * Регистрация экземпляра с использованием PreloadedExtension
             */
            new PreloadedExtension([$type], []),
        ];
    }


    public function testSubmitValidData(): void
    {
        /* DATA */
        $name = "test";
        $message = 'Test message';
        $phone = '+79999999999';
        $code = '1234567';

        /* FORM */
        $model = new SupportFeedbackDTO();

        $form = $this->factory->create(SupportFeedbackForm::class, $model);

        $formData = [
            'name' => $name,
            'message' => $message,
            'phone' => $phone,
            'code' => $code,
        ];

        $form->submit($formData);

        self::assertTrue($form->isSynchronized());

        /* OBJECT */
        $expected = new SupportFeedbackDTO();
        $expected->setName($name);
        $expected->setMessage($message);
        $expected->setPhone($phone);
        $expected->setCode($code);

        self::assertEquals($expected, $model);

        /* VIEW */
        $view = $form->createView();
        $children = $view->children;

        foreach(array_keys($formData) as $key)
        {
            self::assertArrayHasKey($key, $children);
        }

        self::assertTrue(true);
    }
}