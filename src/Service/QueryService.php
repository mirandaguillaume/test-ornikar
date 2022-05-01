<?php

namespace App\Service;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\Query;
use App\Repository\InstructorRepository;
use App\Repository\MeetingPointRepository;

class QueryService
{
    public function __construct(
        private readonly ApplicationContext $applicationContext,
        private readonly MeetingPointRepository $meetingPointRepository,
        private readonly InstructorRepository $instructorRepository,
    ) {
    }

    public function createQuery(array $data): Query
    {
        $query = new Query();

        if (isset($data['lesson']) && $data['lesson'] instanceof Lesson) {
            $query->lesson = $data['lesson'];
            $query->lessonInstructor = $this->instructorRepository->getById($query->lesson->instructorId);
            $query->lessonMeetingPoint = $this->meetingPointRepository->getById($query->lesson->meetingPointId);
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
