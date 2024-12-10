<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'short_name', 'website', 'address', 'logo', 'config_document'
    ];

    public function uat()
{
    return $this->hasMany(Uat::class);
}

}

