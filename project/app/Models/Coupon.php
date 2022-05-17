<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'price','coupon_price_range', 'times', 'start_date','end_date'];
    public $timestamps = false;
}
