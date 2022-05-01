<?php

namespace App\Context;

use App\Entity\Learner;
use App\Helper\SingletonTrait;

class ApplicationContext
{
    /**
     * @var Learner
     */
    private Learner $currentUser;

    /**
     * @return Learner
     */
    public function getCurrentUser(): Learner
    {
        return $this->currentUser;
    }

    /**
     * @param Learner $currentUser
     */
    public function setCurrentUser(Learner $currentUser): void
    {
        $this->currentUser = $currentUser;
    }
}
