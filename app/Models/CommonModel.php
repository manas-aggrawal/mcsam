<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommonModel extends Model
{
    use HasFactory;
    /**
     * @param $user
     */
    public function createToken()
    {
        
        return Str::random(60);
    }
}
