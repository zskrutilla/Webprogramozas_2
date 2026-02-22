<?php

declare(strict_types=1);
require_once __DIR__ . '/Notifier.php';

/**
 * RegistrationService – üzleti logika.
 * OOP újdonság: dependency injection (Notifier-t kívülről kapja a konstruktorban).
 */
final class RegistrationService
{
    public function __construct(private Notifier $notifier) {}

    /**
     * @return array{ok:bool, errors:array<string,string>}
     */
    public function register(string $name, string $email): array
    {
        $errors = [];
        $name = trim($name);
        $email = trim($email);

        if ($name === '') $errors['name'] = 'Kérem, adja meg a nevét.';
        if ($email === '') $errors['email'] = 'Kérem, adja meg az e-mail címét.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Az e-mail formátuma nem megfelelő.';

        if ($errors) return ['ok' => false, 'errors' => $errors];

        $this->notifier->notify($email, 'Sikeres regisztráció', 'Kedves ' . $name . '! A regisztráció sikeres.');
        return ['ok' => true, 'errors' => []];
    }
}
