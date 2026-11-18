<?php

namespace App\Enums;

enum OwnershipStatus: string
{
    case Draft = 'Draft';
    case Finalised = 'Finalised';
    case Superseded = 'Superseded';
}
