<?php

namespace Modules\Events\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\Events\Models\EventParticipant;

class ParticipantsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $guest;

    public function __construct($guest) {
        $this->guest = $guest;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->guest;
    }

    /**
    * @var EventParticipant $guest
    */
    public function map($guest): array
    {
        return [
           $guest->name, 
           $guest->company, 
           $guest->designation, 
           $guest->unit, 
           $guest->invitee_type->name,
           $guest->pack_count,
           $guest->admit_count
        ];
    }

    /**
     * Write code on Method
     *
     * @return response()
    */
    public function headings(): array
    {
        return [
            'Name',
            'Company',
            'Designation',
            'Unit',
            'Type',
            'No. Invitees',
            'Attended'
        ];
    }

}