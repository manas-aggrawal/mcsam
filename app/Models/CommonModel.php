<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
