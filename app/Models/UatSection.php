<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UatSection extends Model
{
    use HasFactory;

    protected $table = 'uat_sections';

    protected $fillable = [
        'uat_id',
        'page_id',
        'section_on_pages',
        'url',
        'cms_admin_panel',
        'test_result',
        'note',
        'note_image', // Tambahkan kolom ini
    ];

    public function uat()
    {
        return $this->belongsTo(Uat::class);
    }
    
    public function Pages()
    {
        return $this->belongsTo(UatPage::class);
    }

    public function subSections()
    {
        return $this->hasMany(UatSubSection::class, 'section_id'); // Ganti 'section_id' jika nama kolom berbeda
    }
}
