<?php

namespace App\Context;

use App\Entity\Learner;
use App\Helper\SingletonTrait;

class ApplicationContext
{
    private Learner $currentUser;

    public function getCurrentUser(): Learner
    {
        return $this->currentUser;
    }

    public function setCurrentUser(Learner $currentUser): void
    {
        $this->currentUser = $currentUser;
    }
}
