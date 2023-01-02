<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order_record';
    protected $primaryKey = 'id';
     public function userdata(){
    	 return $this->hasone('App\User', 'id', 'user_id');
    }
    
}
?>