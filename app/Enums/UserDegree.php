<?php 

declare(strict_types=1);

namespace App\Enums;

enum UserDegree: string 
{
    case SMK = 'SMK';
    case SMA = 'SMA';
    case S1  = 'S1';
    case S2  = 'S2';
    case S3  = 'S3';
}