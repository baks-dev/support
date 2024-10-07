<?php

namespace BaksDev\Support\Entity\Modify;

use BaksDev\Core\Type\Modify\ModifyAction;

interface SupportModifyInterface
{
    public function getAction(): ModifyAction;
}
