<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\ThirdPartyCompany;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ThirdPartyCompanyExport implements FromCollection, WithHeadings
{
  protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = ThirdPartyCompany::query();

        if ($this->search) {
            $query->where('company_name', 'like', "%{$this->search}%");
        }
        return $query->select(
            'id',
            'company_name',
            'company_email',
            'company_phone',
            'company_address',
            'status'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Company Name',
            'Company Email',
            'Company Phone',
            'Company Address',
            'Status',
        ];
    }
}
