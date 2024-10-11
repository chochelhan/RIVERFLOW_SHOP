<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{


    public function getJWTIdentifier(): int {
        return $this->getKey();
    }
    public function getJWTCustomClaims(): array {

        return [];
    }
}
