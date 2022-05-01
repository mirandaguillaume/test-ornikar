<?php

namespace App\Entity;

class Lesson
{
    public function __construct(
        public int $id,
        public int $meetingPointId,
        public int $instructorId,
        public \DateTime $start_time,
        public \DateTime  $end_time
    ) {
    }
}
