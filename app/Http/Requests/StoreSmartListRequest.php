<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSmartListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'criteria' => ['nullable', 'array'],
            'criteria.type' => ['required_with:criteria', 'string', 'in:tag,group'],
            'criteria.tag' => ['required_if:criteria.type,tag', 'string'],
            'criteria.operator' => ['required_if:criteria.type,tag', 'string', 'in:equals,not_equals'],
            'criteria.logic' => ['required_if:criteria.type,group', 'string', 'in:and,or'],
            'criteria.conditions' => ['required_if:criteria.type,group', 'array'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $raw = $this->input('criteria');

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                if (array_is_list($decoded)) {
                    $decoded = $decoded[0] ?? [];
                }

                $this->merge([
                    'criteria' => $decoded,
                ]);
            }
        }
    }
}
