<?php

namespace App\Entity;

class Instructor
{
    public function __construct(
        public int $id,
        public string $firstname,
        public string $lastname
    ) {
    }
}
