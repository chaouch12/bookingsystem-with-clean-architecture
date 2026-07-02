<?php

declare(strict_types=1);

namespace App\Layers\Domain\Appartment\Enum;

enum Amenity: int
{
    case WIFI = 1;
    case AirConditioning = 2;
    case Parking = 3;
    case PetFriendly = 4;
    case SwimmingPool = 5;
    case Gym = 6;
    case Spa = 7;
    case Terrace = 8;
    case MountainView = 9;

}
