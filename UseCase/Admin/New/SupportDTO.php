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

namespace BaksDev\Support\UseCase\Admin\New;

use BaksDev\Support\Entity\Event\SupportEventInterface;
use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\Type\Priority\SupportPriority;
use BaksDev\Support\Type\Status\SupportStatus;
use BaksDev\Support\UseCase\Admin\New\Invariable\SupportInvariableDTO;
use BaksDev\Support\UseCase\Admin\New\Message\SupportMessageDTO;
use BaksDev\Support\UseCase\Admin\New\Token\SupportTokenDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @see SupportEvent */
final class SupportDTO implements SupportEventInterface
{
    /**  Идентификатор События */
    #[Assert\Uuid]
    private ?SupportEventUid $id = null;


    /** Постоянная величина */
    private ?SupportInvariableDTO $invariable;

    /** Статус тикета */
    #[Assert\NotBlank]
    private SupportStatus $status;


    /** Приоритет тикета */
    #[Assert\NotBlank]
    private SupportPriority $priority;


    /** Сообщения */
    private ArrayCollection $messages;


    /** Токен маркетплейса */
    private SupportTokenDTO $token;


    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->token = new SupportTokenDTO();
        $this->invariable = new SupportInvariableDTO();
    }

    public function getEvent(): ?SupportEventUid
    {
        return $this->id;
    }


    public function setId(SupportEventUid $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getInvariable(): ?SupportInvariableDTO
    {
        return $this->invariable;
    }

    public function getToken(): SupportTokenDTO
    {
        return $this->token;
    }

    public function setToken(SupportTokenDTO $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function setInvariable(?SupportInvariableDTO $invariable): self
    {
        $this->invariable = $invariable;
        return $this;
    }

    /** @return  ArrayCollection<SupportMessageDTO> */
    public function getMessages(): ArrayCollection
    {
        return $this->messages;
    }


    public function addMessage(Message\SupportMessageDTO $message): bool
    {
        /**
         * Проверяем, что такой текст уже был добавлен
         */

        $filter = $this->messages->filter(function(Message\SupportMessageDTO $element) use ($message) {

            $clean1 = mb_strtolower(preg_replace('/\s+/', '', $message->getMessage()));
            $clean2 = mb_strtolower(preg_replace('/\s+/', '', $element->getMessage()));

            return
                $element->getDate()->format('YmdHi') === $message->getDate()->format('YmdHi') &&
                md5($clean1) === md5($clean2) &&
                $message->getOut() === $element->getOut();
        });

        /**
         * Добавляем новое сообщение если текст не был найден
         */

        if($filter->isEmpty())
        {
            $this->messages->add($message);
            return true;
        }

        return false;
    }

    public function removeProperty(Message\SupportMessageDTO $message): void
    {
        $this->messages->removeElement($message);
    }

    public function getStatus(): SupportStatus
    {
        return $this->status;
    }

    public function setStatus(SupportStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getPriority(): SupportPriority
    {
        return $this->priority;
    }

    public function setPriority(SupportPriority $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

}