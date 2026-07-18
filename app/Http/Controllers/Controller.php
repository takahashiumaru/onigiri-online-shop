<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public static function getVersion(): string
    {
        return json_decode(file_get_contents(base_path('composer.json')), true)['version'] ?? '1.0.0';
    }
}
