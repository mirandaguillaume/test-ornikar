<?php

namespace App\Service;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\Query;

class QueryService
{
    public function __construct(
        private readonly ApplicationContext $applicationContext
    ) {}

    public function createQuery(array $data): Query
    {
        $query = new Query();

        if (isset($data['lesson']) && $data['lesson'] instanceof Lesson) {
            $query->lesson = $data['lesson'];
        }

        if (isset($data['instructor']) && $data['instructor'] instanceof Instructor) {
            $query->instructor = $data['instructor'];
        }

        if (isset($data['user']) && $data['user'] instanceof Learner) {
            $query->learner = $data['user'];
        } else {
            $query->learner = $this->applicationContext->getCurrentUser();
        }

        return $query;
    }
}
