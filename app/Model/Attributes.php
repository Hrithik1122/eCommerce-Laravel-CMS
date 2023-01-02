<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Attributes extends Model
{
    protected $table = 'attributes';
    protected $primaryKey = 'id';

     public function setname()
    {      
        return $this->hasone('App\Model\AttributeSet', 'id', 'att_set_id');
    }
}
?>