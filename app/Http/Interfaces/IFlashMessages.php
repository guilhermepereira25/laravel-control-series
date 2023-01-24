<?php

namespace App\Http\Interfaces;

use Illuminate\Http\Request;

interface IFlashMessages
{
    public function setFlashMessages(Request $request, string $key, string $message);

    public function manipulateFlashMessages(Request $request, string $key);
}