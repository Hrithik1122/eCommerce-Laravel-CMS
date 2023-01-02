<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $table = 'wishlist';
    protected $primaryKey = 'id';

     public function productdata()
    {      
        return $this->hasone('App\Model\Product', 'id', 'product_id');
    }
}
?>