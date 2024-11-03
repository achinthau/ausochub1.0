<?php

namespace App\Models;

use App\Models\Pos\BillHeader;
use App\Models\Pos\BillTran;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use HasFactory;

    protected $attributes = [
        'ticket_category_id' => 0,
        'ticket_sub_category_id' => 0,
        'ticket_status_id' => 1,
        'outlet_id' => 0,
        'crm' => 1,
        // 'tags' => []
    ];

    protected $casts = [
        'tags' => 'array'
    ];

    protected $appends = ['order_total'];


    public function getTicketRefAttribute()
    {
        return ($this->ticket_sub_category_id * 100000) + str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getTicketTitleAttribute()
    {
        return ($this->ticket_category_id == 3 ? "Order" : "Ticket") . " #" . $this->ticket_ref;
    }

    public function getBorderColorAttribute()
    {
        if ($this->ticket_category_id == 3 && !$this->is_synced && $this->ticket_status_id == 1) {
            return "border-red-300";
        }
        if ($this->ticket_category_id == 3 && ($this->is_synced = 0 || $this->is_synced = null) && $this->ticket_status_id > 1) {
            return "border-orange-300";
        }
        return "border-{$this->status->color}-300";
    }

    public function getBorderColorHoverAttribute()
    {
        if ($this->ticket_category_id == 3 && !$this->is_synced && $this->ticket_status_id == 1) {
            return "border-red-500";
        }
        if ($this->ticket_category_id == 3 && ($this->is_synced = 0 || $this->is_synced = null) && $this->ticket_status_id > 1) {
            return "border-orange-500";
        }
        return "border-{$this->status->color}-500";
    }

    public function getStandardDueAtAttribute()
    {
        return $this->due_at ?? $this->created_at->addMinutes(30);
    }

    public function getOrderTotalAttribute()
    {
        return $this->items->sum('line_total');
    }

    public function logActivity($type,$comment = null)
    {
        $ticketActivity = new TicketActivity;
        $ticketActivity->ticket_id = $this->id;
        $ticketActivity->type = $type;
        $ticketActivity->comment = $comment;
        $ticketActivity->user_id = Auth::check() ? Auth::id() : 1;
        $ticketActivity->save();
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(TicketSubCategory::class, 'ticket_sub_category_id');
    }

    public function items()
    {
        return $this->hasMany(TicketItem::class, 'ticket_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function status()
    {
        return $this->belongsTo(TicketStatus::class, 'ticket_status_id');
    }


    public function createPosOrder()
    {
        $billHeader = new BillHeader;

        $billNo = BillHeader::max("bill_no") + 1;
        $billHeader->bill_no = $billNo;
        // $billHeader->mech_no = "1";
        // $billHeader->clrk_code = "10";
        $billHeader->bill_date = Carbon::now()->format('Y-m-d') . " 00:00:00.000"; //"2023-03-08 00:00:00.000";
        $billHeader->bill_amt = $this->items->sum('line_total');
        // $billHeader->shift_no = "M";
        //$billHeader->bill_type = "S";
        //$billHeader->pay_mode = "C";
        //$billHeader->bill_valid = "Y";
        //$billHeader->adv_amt = ".000";
        $billHeader->bill_start_time = Carbon::now()->format('H:i'); //"14:58";
        $billHeader->bill_end_time = Carbon::now()->format('H:i'); //"14:58";
        // $billHeader->upd_flag = "N";
        // $billHeader->paid_amt = ".000";
        // $billHeader->bal_amt = "800.000";
        // $billHeader->bill_ref = "3689";
        $billHeader->sale_date = Carbon::now()->format('Y-m-d H:i:s') . ".000";
        // $billHeader->tax = ".000";
        // $billHeader->cCode = "0013632   ";
        $billHeader->Table_No = "3001";
        $billHeader->Attend_By = "";
        // $billHeader->Ord_Type = "DE";
        // $billHeader->Service_Type = "A";
        // $billHeader->No_Of_Pax = "1";
        // $billHeader->Service_charge_Perc = "0";
        // $billHeader->Service_charge_Amt = ".000";
        $billHeader->loc_id = "001"; //Need to add pos location id
        // $billHeader->room_no = "";
        // $billHeader->guest_id = null;
        // $billHeader->remarks = ""; //Order Remark need to show here
        // $billHeader->bill_mode = "NO";
        // $billHeader->Pay_type = "C";
        // $billHeader->delivery_no = "0";
        // $billHeader->TakeAway_No = "0";
        // $billHeader->Catering_No = "0";

        // ******************* $billHeader->KOT_Ref = "3"; Need to discuss
        $billHeader->Discount_Perc = "0"; // 5% => 5
        $billHeader->Discount_Amt = ".000"; // 100 Rs of => 100
        $billHeader->split_no = "0";
        $billHeader->printcount = "0 ";
        $billHeader->Active = "N";
        $billHeader->C_inst1 = "0                   ";
        $billHeader->C_inst2 = "                    "; //Online Order Number => first Name
        $billHeader->ethnic_grp = "  ";
        $billHeader->dely_order_ref = null;
        $billHeader->discount_index = "";
        $billHeader->tax_perc = "";
        $billHeader->loy_curPoints = ".000";
        $billHeader->loy_newPoints = ".000";
        $billHeader->status = "";
        $billHeader->allowDelvCharge = "";
        $billHeader->deliveryCharge = ".000";
        $billHeader->dispatchedTime = "";
        $billHeader->deliveredTime = "";
        $billHeader->sync_cloud = "0";
        // $sql = "INSERT INTO bill_tran ('bill_no','mech_no','clrk_code','shift_no','bill_date','tran_type','tran_code','tran_desc','tran_qty','unit_price','tran_amt','tran_valid','price_type','tran_time','nfplu','unitqty','tran_ref','line_no','tran_cat','type_code','key_code','dept_code','class_id','tran_amt2','batch_no','itemStock','tran_date','tran_stat','tax_code','tax_amount','disc_amt','Prod_id','Table_No','Attend_By','Order_type','Loc_id','KOTNo','Parent_LineNo','Split','Sale_Type','Unit_code','Printer_No','Item_Unit','kPrint_Slevel','cover_no','S_charge_amt','grp_code','approved_by','buffer','bill_mod') VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
        $billHeader->exported = "0";
        // dd($billHeader);
        $billHeader->save();

        $this->synced_at = Carbon::now();
        $this->is_synced = 1;
        $this->bill_no = $billNo;

        $this->save();

        $kotNo = BillTran::max('KOTNo') + 1;

        foreach ($this->items as $key => $item) {



            $billTran = new BillTran;
            $billTran->bill_no = $billNo;
            $billTran->mech_no = "1";
            $billTran->clrk_code = "10";
            $billTran->shift_no = "M";
            $billTran->bill_date = $billHeader->bill_date;
            $billTran->tran_type = "S";
            $billTran->tran_code = $item->item->barcode;
            $billTran->tran_desc = $item->item->descr;
            $billTran->tran_qty = $item->qty;
            $billTran->unit_price = $item->unit_price;
            $billTran->tran_amt = $item->line_total;
            $billTran->tran_valid = "Y";
            $billTran->price_type = "1";
            $billTran->tran_time = Carbon::now()->format('H:i');;
            $billTran->nfplu = "N";
            $billTran->unitqty = $item->qty;
            $billTran->tran_ref = "          ";
            $billTran->line_no = $key + 1;
            $billTran->tran_cat = "S";
            $billTran->type_code = "RS";
            $billTran->key_code = "NN";
            $billTran->dept_code = $item->item->dept_code;
            $billTran->class_id = $item->item->class_id;
            $billTran->tran_amt2 = $item->line_total;
            $billTran->batch_no = "";
            $billTran->itemStock = "1";
            $billTran->tran_date = Carbon::now()->format('Y-m-d') . " 00:00:00.000";;
            $billTran->tran_stat = "S";
            $billTran->tax_code = "00";
            $billTran->tax_amount = ".000";
            $billTran->disc_amt = ".000";
            $billTran->Prod_id = $item->item->prod_id;
            $billTran->Table_No = $billHeader->Table_No;
            $billTran->Attend_By = "10";
            $billTran->Order_type = "DE";
            $billTran->Loc_id = "001";
            $billTran->KOTNo = $kotNo;
            $billTran->Parent_LineNo = "1";
            $billTran->Split = "NO";
            $billTran->Sale_Type = "2";
            $billTran->Unit_code = "1";
            $billTran->Printer_No = "24";
            $billTran->Item_Unit = "   ";
            $billTran->kPrint_Slevel = "0";
            $billTran->cover_no = "1";
            $billTran->S_charge_amt = ".000";
            $billTran->grp_code = " ";
            $billTran->approved_by = "";
            $billTran->buffer = null;
            $billTran->bill_mode = "NO";
            $billTran->save();



            // $sql = "INSERT INTO bill_tran ('bill_no','mech_no','clrk_code','shift_no','bill_date','tran_type','tran_code','tran_desc','tran_qty','unit_price','tran_amt','tran_valid','price_type','tran_time','nfplu','unitqty','tran_ref','line_no','tran_cat','type_code','key_code','dept_code','class_id','tran_amt2','batch_no','itemStock','tran_date','tran_stat','tax_code','tax_amount','disc_amt','Prod_id','Table_No','Attend_By','Order_type','Loc_id','KOTNo','Parent_LineNo','Split','Sale_Type','Unit_code','Printer_No','Item_Unit','kPrint_Slevel','cover_no','S_charge_amt','grp_code','approved_by','buffer','bill_mod') VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";

            // DB::connection('mr-kottu-sqlsrv')->insert($sql, [$billTran->bill_no, $billTran->mech_no, $billTran->clrk_code, $billTran->shift_no, $billTran->bill_date, $billTran->tran_type, $billTran->tran_code, $billTran->tran_desc, $billTran->tran_qty, $billTran->unit_price, $billTran->tran_amt, $billTran->tran_valid, $billTran->price_type, $billTran->tran_time, $billTran->nfplu, $billTran->unitqty, $billTran->tran_ref, $billTran->line_no, $billTran->tran_cat, $billTran->type_code, $billTran->key_code, $billTran->dept_code, $billTran->class_id, $billTran->tran_amt2, $billTran->batch_no, $billTran->itemStock, $billTran->tran_date, $billTran->tran_stat, $billTran->tax_code, $billTran->tax_amount, $billTran->disc_amt, $billTran->Prod_id, $billTran->Table_No, $billTran->Attend_By, $billTran->Order_type, $billTran->Loc_id, $billTran->KOTNo, $billTran->Parent_LineNo, $billTran->Split, $billTran->Sale_Type, $billTran->Unit_code, $billTran->Printer_No, $billTran->Item_Unit, $billTran->kPrint_Slevel, $billTran->cover_no, $billTran->S_charge_amt, $billTran->grp_code, $billTran->approved_by, $billTran->buffer, $billTran->bill_mode]);
        }
    }

    public function activities()
    {
        return $this->hasMany(TicketActivity::class)->latest();
    }

    public function scopeSearch($query, $key)
    {
        empty($key) ? $query :  $query->where(function ($query) use ($key) {
            $query->orWhere('id', 'like', "%$key%")
                ->orWhereIn('lead_id', function ($query) use ($key) {
                    $query->select('id')->from('leads')->where('contact_number', 'like', "%$key%");
                });
        });
    }

    public function scopeByStatus($query, $statues)
    {
        empty($statues) ? $query :  $query->whereIn('ticket_status_id', $statues);
    }

    public function scopeGeneral($query)
    {
        $query->whereIn('ticket_category_id', [1, 2]);
    }

    public function scopeOrders($query)
    {
        $query->whereIn('ticket_category_id', [3]);
    }

    public function scopeToday($query, $from = null, $to = null)
    {

        if (!($from && $to)) {
            // $query->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()]);
            $query;
        }

        if ($from && !$to) {
            $query->where('created_at', '>', $from);
        }

        if (!$from && $to) {
            $query->where('created_at', '<', $to);
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    public function scopeByOutlet($query, $outlet)
    {
        $outlet ? $query->where('outlet_id', $outlet) : $query;
    }

    public function scopeRelavant($query)
    {
        Auth::user()->can('outlet-user') ? $query->where('outlet_id', Auth::user()->outlet_id) : $query;
    }
}
