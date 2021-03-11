<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    static public function get_last_date($id)
	{
	   if(Customer_detail::where('customer_id',$id)->count() > 0 ) {	
		$cust = Customer_detail::where('customer_id',$id)->orderby('id','DESC')->first();
		$res = $cust->inv_date;
		} else
			$res = '-';
		
		return $res;
		
	}
}
