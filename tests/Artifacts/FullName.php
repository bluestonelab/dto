<?php

namespace Tests\Artifacts;

class FullName
{
    public function __construct(
        public string $firstname,
        public string $lastname
    ) {
    }
}
