<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouses';


    public function materials(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

}
