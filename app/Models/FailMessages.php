<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailMessages extends Model
{
    use HasFactory;
    protected $fillable = ['method', 'message'];

    public static function registerFailMessage(string $method, string $message): void
    {
        $model = new self();
        $model->create([
            'method' => $method,
            'message' => $message
        ]);
    }
}
