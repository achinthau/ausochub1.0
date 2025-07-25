<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CxTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'category', 'product', 'model', 'work_order_no', 'service_center', 'warranty_status', 'sold_date',
        'customer_name', 'customer_address', 'customer_contact_01', 'customer_contact_02',
        'technician_name', 'technician_contact', 'supervisor_name', 'supervisor_contact', 'status', 'creator',
        'satisfaction_rate','satisfaction_reasons','dis_satisfaction_reasons','cancelling_reasons',
        'closed_by', 'surveyed_by', 'company', 'reopened_by', 'reopened_reasons'
    ];
    
}
