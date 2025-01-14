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

namespace BaksDev\Support\UseCase\Public\Feedback;

use BaksDev\Field\Pack\Phone\Type\PhoneField;
use Symfony\Component\Validator\Constraints as Assert;

final class SupportFeedbackDTO
{
    /** Капча */
    #[Assert\NotBlank(groups: ['anonymous_user'])]
    private bool $captcha = false;

    #[Assert\NotBlank(groups: ['anonymous_user'])]
    public ?string $code = null;


    public function __construct(
        #[Assert\NotBlank(message: 'Имя обязательно для заполнения')]
        public ?string $name = null,

        #[Assert\NotBlank(message: 'Поле "Телефон" обязательно для заполнения')]
        public PhoneField|string|null $phone = null,

        #[Assert\NotBlank(message: 'Поле "Сообщение" обязательно для заполнения')]
        public ?string $message = null,
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getPhone(): PhoneField|string|null
    {
        return $this->phone;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setPhone(PhoneField|string|null $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function isCaptcha(): bool
    {
        return $this->captcha;
    }

    public function setCaptcha(bool $captcha): self
    {
        $this->captcha = $captcha;
        return $this;
    }

    public function captchaValid(): void
    {
        $this->captcha = true;
    }

    /**
     * Code
     */
    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

}