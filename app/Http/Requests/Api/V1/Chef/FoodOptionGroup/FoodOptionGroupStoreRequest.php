<?php

namespace App\Http\Requests\Api\V1\Chef\FoodOptionGroup;

use App\Enums\Chef\FoodOptionGroup\FoodOptionGroupSelectTypeEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $food_slug
 * @property string $name
 * @property string $selection_type
 * @property int $max_selection
 * @property boolean $is_required
 */
class FoodOptionGroupStoreRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'food_slug' => 'required|exists:foods,slug',
            'name' => 'required|string|max:255',
            'selection_type' => ['required', Rule::in(FoodOptionGroupSelectTypeEnum::values())],
            'max_selections' => [
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $selectionType = $this->input('option_group.selection_type');
                    $minSelections = $this->input('option_group.min_selections');

                    if ($selectionType === 'single' && $value !== 1) {
                        $fail('Max selections must be 1 for single selection type.');
                    }
                    if ($value !== null && $minSelections > $value) {
                        $fail('Max selections must be greater than or equal to min selections.');
                    }
                },
            ],
            'is_required' => 'boolean',
        ];
    }


    protected function prepareForValidation(): void
    {
        if (isset($this->selection_type) and $this->selection_type == "single") {
            $this->merge([
                'max_selections' => 1,
            ]);
        }
    }

}