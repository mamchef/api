<?php

namespace App\Http\Requests\Traits;

trait MultilingualValidationTrait
{
    /**
     * Get a localized validation message.
     *
     * @param string $lithuanian
     * @param string $english
     * @return string
     */
    protected function getLocalizedMessage(string $lithuanian, string $english): string
    {
        return app()->getLocale() === 'lt' ? $lithuanian : $english;
    }

    /**
     * Get localized error message for food validation.
     */
    protected function getFoodValidationMessages(): array
    {
        if (app()->getLocale() === 'lt') {
            return [
                'food_unavailable' => "Maistas ':name' šiuo metu neprieinamas.",
                'food_quantity_exceeded' => "Tik :qty ':name' porcijos yra prieinamos.",
                'food_not_found' => "Pasirinktas maistas neegzistuoja.",
                'food_different_store' => "Visi maisto produktai turi priklausyti tai pačiai virtuvei.",
                'option_group_mismatch' => "Parinkčių grupė nepriklauso šiam maistui.",
                'option_not_in_group' => "Parinktis nepriklauso pasirinktai grupei.",
                'required_option_missing' => "':group' pasirinkimas yra privalomas ':food' maistui.",
                'option_quantity_exceeded' => "Maksimaliai :max leidžiama ':option' parinktimis.",
            ];
        }

        return [
            'food_unavailable' => "The food ':name' is currently unavailable.",
            'food_quantity_exceeded' => "Only :qty portions of ':name' are available.",
            'food_not_found' => "Selected food does not exist.",
            'food_different_store' => "All foods must belong to the same chef store.",
            'option_group_mismatch' => "Option group does not belong to this food.",
            'option_not_in_group' => "Option does not belong to the selected group.",
            'required_option_missing' => "':group' selection is required for ':food'.",
            'option_quantity_exceeded' => "Maximum :max allowed for ':option'.",
        ];
    }

    /**
     * Get localized error message for kitchen/chef store validation.
     */
    protected function getKitchenValidationMessages(): array
    {
        if (app()->getLocale() === 'lt') {
            return [
                'kitchen_closed' => "Virtuvė uždaryta. Darbo valandos: :start - :end",
                'kitchen_not_found' => "Pasirinkta virtuvė neegzistuoja.",
                'invalid_delivery_method' => "Ši virtuvė palaiko tik: :method",
                'daily_limit_reached' => "Ši virtuvė pasiekė maksimalų dieninį užsakymų limitą (:max užsakymai). Bandykite dar kartą rytoj.",
                'invalid_address' => "Pasirinktas neteisingas adresas.",
            ];
        }

        return [
            'kitchen_closed' => "Kitchen is closed. Operating hours: :start - :end",
            'kitchen_not_found' => "Selected kitchen does not exist.",
            'invalid_delivery_method' => "This kitchen only supports: :method",
            'daily_limit_reached' => "This kitchen has reached its maximum daily order limit (:max orders). Please try again tomorrow.",
            'invalid_address' => "Invalid address selected.",
        ];
    }

    /**
     * Get localized error message for user validation.
     */
    protected function getUserValidationMessages(): array
    {
        if (app()->getLocale() === 'lt') {
            return [
                'invalid_phone' => 'Prašome įvesti galiojantį Lietuvos telefono numerį.',
                'invalid_postal_code' => 'Prašome įvesti galiojantį Lietuvos pašto kodą (LT-XXXXX).',
                'id_number_digits' => 'turi būti tiksliai 11 skaitmenų.',
                'invalid_email' => 'Prašome įvesti galiojantį el. pašto adresą.',
                'password_weak' => 'Slaptažodis turi turėti bent 8 simbolius, raidžių ir skaičių.',
            ];
        }

        return [
            'invalid_phone' => 'Please enter a valid Lithuanian phone number.',
            'invalid_postal_code' => 'Please enter a valid Lithuanian postal code (LT-XXXXX).',
            'id_number_digits' => 'must be exactly 11 digits.',
            'invalid_email' => 'Please enter a valid email address.',
            'password_weak' => 'Password must be at least 8 characters with letters and numbers.',
        ];
    }

    /**
     * Replace placeholders in localized messages.
     */
    protected function replacePlaceholders(string $message, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            $message = str_replace(":{$key}", $value, $message);
        }

        return $message;
    }
}