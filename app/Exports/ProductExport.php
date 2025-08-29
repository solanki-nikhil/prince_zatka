<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    private $where;

    public function __construct($where)
    {
        $this->where = $where;
    }

    public function collection()
    {
        return  Product::select('categories.category_name','sub_categories.sub_category_name','product_name','product_code','quantity','box','price')
        ->leftJoin('categories','categories.id','products.category_id')
        ->leftJoin('sub_categories','sub_categories.id','products.sub_category_id')
        ->whereRaw($this->where)
        ->get();                
    }

    public function headings(): array
    {
        return [
            'Category Name',
            'Sub Category Name',
            'Product Name',
            'Product Code',
            'Quantity',
            'Box',
            'Price',
        ];
    }
}
