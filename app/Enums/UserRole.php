<?php 

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string 
{
    case Admin   = 'ADMIN';
    case Company = 'COMPANY';
    case Talent  = 'TALENT';
}
