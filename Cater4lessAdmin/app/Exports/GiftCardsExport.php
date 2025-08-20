<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\GiftCard;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GiftCardsExport implements FromCollection, WithHeadings
{
 protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function collection()
    {
        $giftCards = GiftCard::query();

        if ($this->query) {
            if (isset($this->query['status'])) {
                $giftCards->where('status', $this->query['status']);
            }
        }

        return $giftCards->select('id', 'code', 'amount', 'balance', 'status', 'expiry_date', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Code',
            'Amount',
            'Balance',
            'Status',
            'Expiry Date',
            'Created At',
        ];
    }

}
