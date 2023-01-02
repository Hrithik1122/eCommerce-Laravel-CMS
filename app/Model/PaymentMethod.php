<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_method';
    protected $primaryKey = 'id';
}
?>