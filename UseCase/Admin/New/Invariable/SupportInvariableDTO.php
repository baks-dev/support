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

namespace BaksDev\Support\UseCase\Admin\New\Invariable;

use BaksDev\Support\Entity\Invariable\SupportInvariableInterface;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Symfony\Component\Validator\Constraints as Assert;

/** @see SupportInvariable */
final class SupportInvariableDTO implements SupportInvariableInterface
{
    /** Тип профиля */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    private TypeProfileUid $type;

    /**  Профиль */
    #[Assert\Uuid]
    private ?UserProfileUid $profile = null;

    /** Id Тикета */
    #[Assert\NotBlank]
    private string $ticket;

    /** Тема, предмет сообщений тикета */
    #[Assert\NotBlank]
    private string $title;


    public function getType(): TypeProfileUid
    {
        return $this->type;
    }

    public function setType(TypeProfileUid $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getProfile(): ?UserProfileUid
    {
        return $this->profile;
    }

    public function setProfile(?UserProfileUid $profile): self
    {
        $this->profile = $profile;
        return $this;
    }

    public function getTicket(): string
    {
        return $this->ticket;
    }

    public function setTicket(string|int $ticket): self
    {
        $this->ticket = (string) $ticket;
        return $this;
    }


    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = empty($title) ? 'Без темы' : $title;
        return $this;
    }

}