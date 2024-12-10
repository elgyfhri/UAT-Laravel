<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditPage extends Model
{
    use HasFactory;

    // Menentukan nama tabel (jika berbeda dengan nama model)
    protected $table = 'edit_pages';

    // Menentukan kolom yang bisa diisi secara massal (Mass Assignment)
    protected $fillable = [
        'navbar_title',
        'navbar_menu1',
        'navbar_menu2',
        'navbar_menu3',
        'card_hero_image',
        'card_hero_title',
        'card_hero_text',
        'hero_title',
        'hero_text',
        'bg_image',
        'image1_section2',
        'image2_section2',
        'image3_section2',
        'title1_section2',
        'title2_section2',
        'title3_section2',
        'text1_section2',
        'text2_section2',
        'text3_section2'
    ];

    // Menentukan kolom timestamp jika Anda tidak ingin menggunakan created_at dan updated_at
    public $timestamps = true; // Secara default adalah true, jadi bisa dihapus jika tidak diubah

    // Jika ada kolom yang tidak ingin diisi secara massal, Anda bisa mendefinisikan $guarded
    // protected $guarded = ['id']; // contoh untuk mencegah ID diubah
}
