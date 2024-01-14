<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public function productMaterials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'product_materials')
            ->withPivot('quantity');
    }


    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'warehouses', 'material_id', 'material_id')
            ->withPivot('remainder', 'price');
    }


}
