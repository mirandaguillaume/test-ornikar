<?php

namespace App\Entity;

class Template
{
    public function __construct(
        public int $id,
        public string $subject,
        public string $content
    ) {}
}
