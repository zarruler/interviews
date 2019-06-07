<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
#use App\Models\CatalogCategory;

class CatalogProduct extends Model
{
    protected $fillable = [
        'name',
        'price'
    ];

    public function categories()
    {
        return $this->belongsToMany(CatalogCategory::class, 'catalog_category_product', 'product_id', 'category_id');
    }
}
