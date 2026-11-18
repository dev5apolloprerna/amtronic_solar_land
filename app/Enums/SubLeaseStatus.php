<?php

namespace App\Enums;

enum SubLeaseStatus: string
{
    case Draft = 'Draft';
    case Active = 'Active';
    case Expired = 'Expired';
    case Cancelled = 'Cancelled';
}
