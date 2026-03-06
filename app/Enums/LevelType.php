<?php 

declare(strict_types=1);

namespace App\Enums;

enum LevelType: string 
{
    case Junior = 'junior';
    case Middle = 'middle';
    case Senior = 'senior';
    case Head   = 'head';

    public function label(): string 
    {
        return match ($this) {
            LevelType::Junior => 'Junior',
            LevelType::Middle => 'Middle',
            LevelType::Senior => 'Senior',
            LevelType::Head   => 'Head'
        };
    }
}