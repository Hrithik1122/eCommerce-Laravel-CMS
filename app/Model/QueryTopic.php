<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class QueryTopic extends Model
{
    protected $table = 'question_query_topic';
    protected $primaryKey = 'id';

     public function Question()
    {      
        return $this->hasmany('App\Model\QueryAns', 'topic_id', 'id');
    }
}
?>