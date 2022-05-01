<?php

namespace App\Service;

use App\Entity\Lesson;

class LessonRenderer
{
    public function renderHtml(Lesson $lesson): string
    {
        return '<p>' . $lesson->id . '</p>';
    }

    public function renderText(Lesson $lesson): string
    {
        return (string) $lesson->id;
    }
}
