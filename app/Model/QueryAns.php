<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class QueryAns extends Model
{
    protected $table = 'query_ans_question';
    protected $primaryKey = 'id';

    public function query_id(){
    	 return $this->hasone('App\Model\QueryTopic', 'id', 'topic_id');
    }
}
?>