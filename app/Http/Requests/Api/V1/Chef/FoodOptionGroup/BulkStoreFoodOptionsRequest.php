<?php

namespace App\Http\Requests\Api\V1\Chef\FoodOptionGroup;

use App\Enums\Chef\FoodOption\FoodOptionTypeEnum;
use App\Enums\Chef\FoodOptionGroup\FoodOptionGroupSelectTypeEnum;
use App\Http\Requests\BaseFormRequest;
use App\Models\FoodOptionGroup;
use Illuminate\Validation\Rule;

class BulkStoreFoodOptionsRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            // Option Group Data
            'option_group' => 'required|array',
            'option_group.food_slug' => 'required|exists:foods,slug',
            'option_group.name' => 'required|string|max:255',
            'option_group.selection_type' => ['required', Rule::in(FoodOptionGroupSelectTypeEnum::values())],
            'option_group.max_selections' => [
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
            'option_group.is_required' => 'boolean',


            'options' => 'required|array|min:1',
            'options.*.name' => 'required|string|max:255',
            'options.*.type' => ['required', Rule::in(FoodOptionTypeEnum::values())],
            'options.*.description' => 'nullable|string|max:225',
            'options.*.price' => 'required|numeric|min:0',
            'options.*.sort_order' => 'integer|min:0',
            'options.*.maximum_allowed' => 'nullable|integer|min:0',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (isset($this->option_group["selection_type"]) and $this->option_group["selection_type"] == "single") {
            $optionGroup = $this->option_group;
            $optionGroup['max_selections'] = 1;
            $this->merge([
                "option_group" => $optionGroup
            ]);
        }

        if ($this->has('options') && is_array($this->options)) {
            $options = $this->options;
            foreach ($options as $index => &$option) {
                if (!isset($option['sort_order'])) {
                    $option['sort_order'] = $index;
                }
            }
            $this->merge(['options' => $options]);
        }
    }
}