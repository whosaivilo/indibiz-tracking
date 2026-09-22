<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingOrder extends Model
{
    use HasFactory;
    protected $guarded = []; // Atau gunakan $fillable untuk menentukan kolom yang bisa diisi massal
}
