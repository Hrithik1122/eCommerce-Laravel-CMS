<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $table = 'deals';
    protected $primaryKey = 'id';
     public function offer()
    {      
        return $this->hasone('App\Model\Offer', 'id', 'offer_id');
    }
}
?>