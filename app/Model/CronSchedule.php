<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CronSchedule extends Model
{
    protected $table = 'update_cron';
    protected $primaryKey = 'id';
}
?>