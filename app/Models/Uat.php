<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uat extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_name',
        'doc_number',
        'revision_number',
        'start_date',
        'end_date',
        'username',
        'password',
        'pages',
        'section_on_pages',
        'sub_section',
        'url',
        'cms_admin_panel',
        'test_result',
        'note',
        'progress_percentage',
        'parent_id'
    ];

    public function parent()
    {
        return $this->belongsTo(Uat::class, 'parent_id');
    }

    public function Pages()
{
    return $this->hasMany(UatPage::class);
}


    public function sections()
    {
        return $this->hasMany(UatSection::class);
    }

    public function subSections()
    {
        return $this->hasMany(UatSubSection::class);
    }

    public function perusahaan()
{
    return $this->belongsTo(Perusahaan::class);
}

public function client()
{
    return $this->belongsTo(Client::class);
}
    
}