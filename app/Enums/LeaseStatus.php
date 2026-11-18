<?php

namespace App\Enums;

enum LeaseStatus: string
{
    case Draft = 'Draft';
    case Active = 'Active';
    case Expired = 'Expired';
    case Terminated = 'Terminated';
}
