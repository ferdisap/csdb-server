<?php

namespace App\Models;

use App\Http\Resources\PassportClientResource;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Client;

class PassportClient extends Client
{
    public function toArray()
    {
        // Gunakan PostResource sebagai default formatter
        return (new PassportClientResource($this))->toArray(request());
    }
}
