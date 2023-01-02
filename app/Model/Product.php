<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';

    public function categoryls()
    {      
        return $this->hasone('App\Model\Categories', 'id', 'category');
    }
    public function subcategoryls()
    {      
        return $this->hasone('App\Model\Categories', 'id', 'subcategory');
    }
    public function brandls()
    {      
        return $this->hasone('App\Model\Brand', 'id', 'brand');
    }
     public function optionls()
    {      
        return $this->hasone('App\Model\ProductOption', 'product_id', 'id');
    }
      public function Attributls()
    {      
        return $this->hasone('App\Model\ProductOption', 'product_id', 'id');
    }
    public function rattingdata()
    {      
        return $this->hasmany('App\Model\Review', 'product_id', 'id');
    }
    
}
?>