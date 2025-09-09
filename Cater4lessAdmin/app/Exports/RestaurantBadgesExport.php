<?php

namespace App\Exports;

use App\Models\Restaurant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RestaurantBadgesExport implements FromCollection, WithHeadings
{
    protected ?string $search;
    protected $badge;

    public function __construct(?string $search = null, $badge = null)
    {
        $this->search = $search;
        $this->badge  = $badge;
    }

    public function collection(): Collection
    {
        $query = Restaurant::select('name', 'badge');

        if (!empty($this->search)) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if (!is_null($this->badge) && $this->badge !== 'all') {
            if ($this->badge === 'none' || $this->badge === '') {
                $query->whereNull('badge');
            } else {
                $query->where('badge', $this->badge);
            }
        }

        return $query->orderBy('name')->get()->map(function ($r) {
            return [
                'Restaurant Name' => $r->name,
                'Badge'           => $r->badge ? ucfirst($r->badge) : 'None',
            ];
        });
    }

    public function headings(): array
    {
        return ['Restaurant Name', 'Badge'];
    }
}
