<?php

namespace App\Enums;

enum ChatIntent: string
{
    case Greeting = 'greeting';
    case Farewell = 'farewell';
}
