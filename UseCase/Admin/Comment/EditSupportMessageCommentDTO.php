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

namespace BaksDev\Support\UseCase\Admin\Comment;

use BaksDev\Support\Entity\Event\SupportEventInterface;
use BaksDev\Support\Type\Event\SupportEventUid;
use BaksDev\Support\UseCase\Admin\Comment\Comment\EditSupportMessageCommentCommentDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class EditSupportMessageCommentDTO implements SupportEventInterface
{
    /**  Идентификатор События */
    #[Assert\Uuid]
    private ?SupportEventUid $id = null;

    /** Комментарий к тикету */
    private readonly EditSupportMessageCommentCommentDTO $comment;

    public function __construct()
    {
        $this->comment = new EditSupportMessageCommentCommentDTO();
    }

    public function getEvent(): ?SupportEventUid
    {
        return $this->id;
    }

    public function getId(): ?SupportEventUid
    {
        return $this->id;
    }

    public function getComment(): EditSupportMessageCommentCommentDTO
    {
        return $this->comment;
    }

    public function setComment(?string $value): self
    {
        $this->comment->setValue($value);
        return $this;
    }
}