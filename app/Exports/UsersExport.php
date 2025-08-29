<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    private $where;

    public function __construct($where)
    {
        $this->where = $where;
    }

    public function collection()
    {
        return  Order::select('name','mobile','address','total_pices','total_box','total_amount', 'bill_number', DB::raw("DATE_FORMAT(orders.created_at,'%d-%m-%Y') AS created_at"))
        ->whereRaw($this->where)
        ->get();                
    }

    public function headings(): array
    {
        return [
            'Name',
            'Mobile',
            'Address',
            'Total Pice',
            'Total Box',
            'Total Amount',
            'Bill Number',
            'Date',
        ];
    }
}
