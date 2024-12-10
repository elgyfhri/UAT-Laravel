<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class UatPage extends Model
{
    use HasFactory;

    protected $table = 'uat_pages'; 

    protected $fillable = [
        'uat_id', 
        'pages', 
        'url',
        'cms_admin_panel',
        'test_result',
        'note',
    ];


    public function uat()
    {
        return $this->belongsTo(Uat::class, 'uat_id', 'id');
    }
    
    // UatPage.php

public function Sections()
{
    return $this->hasMany(UatSection::class, 'page_id');
}


// UatSection.php
public function subSections()
{
    return $this->hasMany(UatSubSection::class);
}

}
