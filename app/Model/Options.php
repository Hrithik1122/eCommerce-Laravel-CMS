<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Options extends Model
{
    protected $table = 'options';
    protected $primaryKey = 'id';


    public function optionlist()
    {      
        return $this->hasmany('App\Model\Optionvalues', 'option_id', 'id');
    }
}
?>