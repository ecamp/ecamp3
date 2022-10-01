<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserStatusChecker implements UserCheckerInterface {
    public function checkPreAuth(UserInterface $user): void {
        if (!$user instanceof User) {
            return;
        }

        if (User::STATE_DELETED === $user->state) {
            throw new CustomUserMessageAccountStatusException('Your user account no longer exists.');
        }

        if (User::STATE_ACTIVATED !== $user->state) {
            throw new CustomUserMessageAccountStatusException('Your user account has not yet been activated. Check your email inbox for a confirmation email.');
        }
    }

    public function checkPostAuth(UserInterface $user): void {
    }
}
