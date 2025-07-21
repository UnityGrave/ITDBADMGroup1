<?php

namespace App\Enums;

enum ProductCondition: string
{
    case NM = 'NM'; // Near Mint
    case LP = 'LP'; // Lightly Played
    case MP = 'MP'; // Moderately Played
    case HP = 'HP'; // Heavily Played
    case DMG = 'DMG'; // Damaged
} 