<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mward extends Model
{
    static public function get_last_date($id)
	{
	   if(Mward_detail::where('mward_id',$id)->count() > 0 ) {
		$cust = Mward_detail::where('mward_id',$id)->orderby('id','DESC')->first();
		$res = $cust->inv_date;
		} else
			$res = '-';
		
		return $res;
		
	}
}
