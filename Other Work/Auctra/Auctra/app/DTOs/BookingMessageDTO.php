<?php

namespace App\DTOs;

class WhatsAppMessageDTO
{
    public function __construct(
        public string $phone,
        public string $template,
        public array $values //? body_1, body_2
    ) {}
}