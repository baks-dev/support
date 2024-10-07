<?php

declare(strict_types=1);

namespace BaksDev\Support\UseCase\Admin\Delete\Modify;

use BaksDev\Core\Type\Modify\Modify\ModifyActionDelete;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Support\Entity\Modify\SupportModifyInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** @see ModifyEvent */
final class ModifyDTO implements SupportModifyInterface
{
    /** Модификатор */
    #[Assert\NotBlank]
    private readonly ModifyAction $action;


    public function __construct()
    {
        $this->action = new ModifyAction(ModifyActionDelete::class);
    }

    public function getAction(): ModifyAction
    {
        return $this->action;
    }

}
