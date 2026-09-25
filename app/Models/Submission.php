<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    // Mendaftarkan kolom mana saja yang diizinkan untuk diisi
    protected $fillable = [
        'form_code',
        'location',
        'start_time',
        'end_time',
        'form_data',
        'final_notes',
        'supervisor_name',
        'provider_name',
        'committee_name',
        'signature_supervisor',
        'signature_provider',
        'signature_committee'
    ];

    // Casting form_data agar otomatis diubah menjadi array PHP saat ditarik dari database
    protected $casts = [
        'form_data' => 'array',
    ];
}