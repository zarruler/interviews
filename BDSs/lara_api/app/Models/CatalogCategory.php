<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
#use App\Models\CatalogProduct;

class CatalogCategory extends Model
{
    protected $fillable = [
        'title'
    ];

    public function products()
    {
        return $this->belongsToMany(CatalogProduct::class, 'catalog_category_product', 'category_id', 'product_id');
    }
}
