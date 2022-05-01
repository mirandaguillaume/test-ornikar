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
    ) {}

    public static function renderHtml(Lesson $lesson): string
    {
        return '<p>' . $lesson->id . '</p>';
    }

    public static function renderText(Lesson $lesson): string
    {
        return (string) $lesson->id;
    }
}
