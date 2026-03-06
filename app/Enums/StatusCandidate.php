<?php 

declare(strict_types=1);

namespace App\Enums;

enum StatusCandidate: string 
{
    case Pending  = 'PENDING';
    case OnReview = 'ON_REVIEW';
    case Accepted = 'ACCEPTED';
    case Rejected = 'REJECTED';
    
    public function label(): string 
    {
        return match ($this) {
            StatusCandidate::Pending  => 'Pending',
            StatusCandidate::OnReview => 'On Review',
            StatusCandidate::Accepted => 'Accepted',
            StatusCandidate::Rejected => 'Rejected',
        };
    }
}