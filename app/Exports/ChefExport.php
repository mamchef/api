<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ChefExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected Collection $chefs;

    public function __construct(Collection $chefs)
    {
        $this->chefs = $chefs;
    }

    /**
     * Return the collection of chefs to export
     */
    public function collection(): Collection
    {
        return $this->chefs;
    }

    /**
     * Define the headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'ID',
            'UUID',
            'ID Number',
            'First Name',
            'Last Name',
            'Full Name',
            'Email',
            'Email Verified At',
            'Phone',
            'Status',
            'Register Source',
            'City ID',
            'City Name',
            'Country',
            'Main Street',
            'Address',
            'ZIP Code',
            'VMVT Number',
            'Language',
            'Stripe Account ID',
            'Stripe Account Status',
            'Stripe Details Submitted',
            'Stripe Payouts Enabled',
            'Stripe Charges Enabled',
            'Stripe Onboarded At',
            'Stripe Onboarding Status',
            'Can Receive Payments',
            'Contract ID',
            'Has Contract',
            'Has Document 1',
            'Has Document 2',
            'Chef Store ID',
            'Chef Store Name',
            'Chef Store Status',
            'Chef Store Rating',
            'Chef Store Phone',
            'Chef Store Address',
            'Chef Store City',
            'Chef Store ZIP',
            'Chef Store Delivery Method',
            'Chef Store Delivery Cost',
            'Chef Store Share Percent',
            'Chef Store Max Daily Order',
            'Chef Store Is Open',
            'Chef Store Estimated Time',
            'Chef Store Start Time',
            'Chef Store End Time',
            'Registered At',
            'Last Updated At',
        ];
    }

    /**
     * Map each chef to a row in the Excel file with comprehensive data
     */
    public function map($chef): array
    {
        $chefStore = $chef->chefStore;
        $city = $chef->city;

        return [
            $chef->id,
            $chef->uuid ?? '',
            $chef->id_number ?? '',
            $chef->first_name ?? '',
            $chef->last_name ?? '',
            $chef->getFullName(),
            $chef->email ?? '',
            $chef->email_verified_at ? $this->dateFormat($chef->email_verified_at) : '',
            $chef->phone ?? '',
            $chef->status?->value ?? '',
            $chef->register_source?->value ?? '',
            $chef->city_id ?? '',
            $city?->name ?? '',
            $city?->country?->name ?? '',
            $chef->main_street ?? '',
            $chef->address ?? '',
            $chef->zip ?? '',
            $chef->vmvt_number ?? '',
            $chef->lang ?? '',
            $chef->stripe_account_id ?? '',
            $chef->stripe_account_status ?? '',
            $chef->stripe_details_submitted ? 'Yes' : 'No',
            $chef->stripe_payouts_enabled ? 'Yes' : 'No',
            $chef->stripe_charges_enabled ? 'Yes' : 'No',
            $chef->stripe_onboarded_at ? $this->dateFormat($chef->stripe_onboarded_at) : '',
            $chef->getStripeOnboardingStatus(),
            $chef->canReceivePayments() ? 'Yes' : 'No',
            $chef->contract_id ?? '',
            !empty($chef->contract) ? 'Yes' : 'No',
            !empty($chef->document_1) ? 'Yes' : 'No',
            !empty($chef->document_2) ? 'Yes' : 'No',
            $chefStore?->id ?? '',
            $chefStore?->name ?? '',
            $chefStore?->status?->value ?? '',
            $chefStore?->rating ?? '',
            $chefStore?->phone ?? '',
            $chefStore?->address ?? '',
            $chefStore?->city?->name ?? '',
            $chefStore?->zip ?? '',
            $chefStore?->delivery_method?->value ?? '',
            $chefStore?->delivery_cost ?? '',
            $chefStore?->share_percent ?? '',
            $chefStore?->max_daily_order ?? '',
            $chefStore?->is_open ? 'Yes' : 'No',
            $chefStore?->estimated_time ?? '',
            $chefStore?->start_daily_time ?? '',
            $chefStore?->end_daily_time ?? '',
            $this->dateFormat($chef->created_at),
            $this->dateFormat($chef->updated_at),
        ];
    }

    /**
     * Apply styles to the Excel file
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Define column widths for better readability
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 25,  // UUID
            'C' => 15,  // ID Number
            'D' => 15,  // First Name
            'E' => 15,  // Last Name
            'F' => 20,  // Full Name
            'G' => 25,  // Email
            'H' => 20,  // Email Verified At
            'I' => 15,  // Phone
            'J' => 15,  // Status
            'K' => 15,  // Register Source
            'L' => 10,  // City ID
            'M' => 20,  // City Name
            'N' => 20,  // Country
            'O' => 20,  // Main Street
            'P' => 30,  // Address
            'Q' => 12,  // ZIP
            'R' => 15,  // VMVT Number
            'S' => 10,  // Language
            'T' => 25,  // Stripe Account ID
            'U' => 20,  // Stripe Account Status
            'V' => 20,  // Stripe Details Submitted
            'W' => 20,  // Stripe Payouts Enabled
            'X' => 20,  // Stripe Charges Enabled
            'Y' => 20,  // Stripe Onboarded At
            'Z' => 25,  // Stripe Onboarding Status
            'AA' => 20, // Can Receive Payments
            'AB' => 20, // Contract ID
            'AC' => 15, // Has Contract
            'AD' => 15, // Has Document 1
            'AE' => 15, // Has Document 2
            'AF' => 12, // Chef Store ID
            'AG' => 25, // Chef Store Name
            'AH' => 20, // Chef Store Status
            'AI' => 15, // Chef Store Rating
            'AJ' => 15, // Chef Store Phone
            'AK' => 30, // Chef Store Address
            'AL' => 20, // Chef Store City
            'AM' => 12, // Chef Store ZIP
            'AN' => 20, // Chef Store Delivery Method
            'AO' => 18, // Chef Store Delivery Cost
            'AP' => 18, // Chef Store Share Percent
            'AQ' => 20, // Chef Store Max Daily Order
            'AR' => 15, // Chef Store Is Open
            'AS' => 18, // Chef Store Estimated Time
            'AT' => 18, // Chef Store Start Time
            'AU' => 18, // Chef Store End Time
            'AV' => 20, // Registered At
            'AW' => 20, // Last Updated At
        ];
    }


    private function dateFormat( $date)
    {
        try {
            return $date->format('Y-m-d H:i:s');
        }catch (\Throwable $exception){
            return $date;
        }
    }
}
