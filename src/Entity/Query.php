<?php

namespace App\Entity;

class Query
{
    public ?Lesson $lesson = null;

    public ?Instructor $instructor = null;

    public Learner $learner;
}
