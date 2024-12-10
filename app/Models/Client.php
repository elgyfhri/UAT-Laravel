<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'short_name', 'address', 'logo'
    ];
    // Di app/Models/Client.php
    public function getLogoAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }

    public function uat()
{
    return $this->hasMany(Uat::class);
}


}

