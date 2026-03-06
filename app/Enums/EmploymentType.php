<?php 

declare(strict_types=1);

namespace App\Enums;

enum EmploymentType: string 
{
    case FullTime = 'full_time';
    case WFH      = 'work_from_home';
    case Remote   = 'remote';
    case Contract = 'contract';

    public function label(): string 
    {
        return match($this) {
            EmploymentType::FullTime => 'Full Time',
            EmploymentType::WFH      => 'Work From Home',
            EmploymentType::Remote   => 'Remote',
            EmploymentType::Contract => 'Contract'
        };
    }
}