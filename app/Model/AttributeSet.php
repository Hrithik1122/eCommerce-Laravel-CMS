<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AttributeSet extends Model
{
    protected $table = 'attribute_set';
    protected $primaryKey = 'id';

    public function attributelist()
    {      
        return $this->hasmany('App\Model\Attributes', 'att_set_id', 'id');
    }
    
}
?>