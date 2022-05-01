<?php

namespace App\Entity;

class Learner
{
    public function __construct(
        public int $id,
        public string $firstname,
        public string $lastname,
        public string $email
    ) {}
}
