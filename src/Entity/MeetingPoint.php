<?php

namespace App\Entity;

class MeetingPoint
{
    public function __construct(
        public int $id,
        public string $url,
        public string $name
    ) {}
}
