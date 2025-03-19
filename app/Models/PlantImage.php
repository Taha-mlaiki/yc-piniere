<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'plant_id',
        'path',
    ];

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
