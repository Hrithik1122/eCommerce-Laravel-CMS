<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FeatureProduct extends Model
{
    protected $table = 'feature_product';
    protected $primaryKey = 'id';
    public function productdata(){
    	 return $this->hasone('App\Model\Product', 'id', 'product_id');
    }
}

?>