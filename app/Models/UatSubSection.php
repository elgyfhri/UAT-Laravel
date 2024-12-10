<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UatSubSection extends Model
{
    use HasFactory;

    protected $table = 'uat_sub_sections';

    protected $fillable = [
        'uat_id',
        'section_id',
        'sub_section',
        'url',
        'cms_admin_panel',
        'test_result',
        'note',
    ];

    public function uat()
    {
        return $this->belongsTo(Uat::class);
    }

    public function Section()
    {
        return $this->belongsTo(UatSection::class);
    }
}