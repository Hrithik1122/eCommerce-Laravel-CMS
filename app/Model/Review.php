<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'id';

     public function product()
    {      
        return $this->hasone('App\Model\Product', 'id', 'product_id');
    }
     public function userdata()
    {      
        return $this->hasone('App\User', 'id', 'user_id');
    }
}
?>