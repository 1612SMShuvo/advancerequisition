<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];
   # protected $fillable = ['conversation_id','message','sent_user','recieved_user','user_id'];
	public function conversation()
	{
	    return $this->belongsTo('App\Models\Conversation')->withDefault(function ($data) {
			foreach($data->getFillable() as $dt){
				$data[$dt] = __('Deleted');
			}
		});
	}
}
