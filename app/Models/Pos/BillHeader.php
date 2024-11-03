<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillHeader extends Model
{
    use HasFactory;

    protected $connection = "mr-kottu-sqlsrv";
    protected $table = "bill_header";

    protected $primaryKey = 'bill_no';
    public $timestamps = false;

    protected $attributes = [
        'mech_no' => "1",
        'clrk_code' => "99",
        'shift_no' => 'M',
        'bill_type' => 'S',
        'pay_mode' => 'C',
        'bill_valid' => 'Y',
        'adv_amt' => '.000',
        'upd_flag' => 'N',
        'paid_amt' => '.000',
        'bal_amt' => '.000',
        'bill_ref' => 'Bill Reference',
        'tax' => '.000',
        'cCode' => '          ',
        'Ord_Type' => 'DE',
        'Service_Type' => 'A',
        'No_Of_Pax' => '0',
        'Service_charge_Perc' => '0',
        'Service_charge_Amt' => '.000',
        'room_no' => '',
        'guest_id' => NULL,
        'bill_mode' => 'NO',
        'Pay_type' => 'C',
        'delivery_no'=>'0',
        'TakeAway_No'=>'0',
        'Catering_No'=>'0',
        'Discount_Perc'=>'0',
        'Discount_Amt' => '.000',
    ];
}
