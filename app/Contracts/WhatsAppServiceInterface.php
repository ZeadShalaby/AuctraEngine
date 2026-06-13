<?php

namespace App\Contracts;

interface WhatsAppServiceInterface
{
    public function sendTemplate(string $phone, string $templateName, array $components);
}