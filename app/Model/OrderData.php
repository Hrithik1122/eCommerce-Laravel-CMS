<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderData extends Model
{
    protected $table = 'order_data';
    protected $primaryKey = 'id';
    public function productdata(){
    	 return $this->hasone('App\Model\Product', 'id', 'product_id');
    }
}

?>