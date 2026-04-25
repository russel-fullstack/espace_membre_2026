<?php

declare(strict_types=1);

enum Role: string
{
    case USER = 'user';
    case ADMIN = 'admin';


public function label() :string {
    return match($this) {
        self::ADMIN => 'Administrateur',
        self::USER => 'Utilisateur',
    };
}

public function isAdmin(): bool {
    return $this === self::ADMIN;   

}
}