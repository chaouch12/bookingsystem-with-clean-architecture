<?php

declare(strict_types=1);

namespace App\Layers\Application\Shared\Notification;

interface EmailService
{
    public function send(string $email, string $subject, string $body): void;
}
