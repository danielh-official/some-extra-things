<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
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
            'id' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in(['To-Do', 'Heading', 'Project', 'Area'])],
            'title' => ['required', 'string', 'max:255'],

            'parent_id' => ['nullable', 'string'],
            'heading_id' => ['nullable', 'string'],

            'is_inbox' => ['sometimes', 'boolean'],

            'start' => ['nullable', 'string', Rule::in(['On Date', 'Anytime', 'Someday'])],
            'start_date' => ['nullable', 'date', 'required_if:start,On Date'],
            'evening' => ['sometimes', 'boolean'],
            'reminder_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date'],

            'tags' => ['nullable'],
            'all_matching_tags' => ['nullable'],

            'status' => ['nullable', 'string', Rule::in(['Open', 'Completed', 'Canceled'])],
            'completion_date' => ['nullable', 'date'],
            'is_logged' => ['sometimes', 'boolean'],

            'notes' => ['nullable', 'string'],
            'checklist' => ['nullable'],

            'creation_date' => ['nullable', 'date'],
            'modification_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        $tags = $this->input('tags');
        if (is_string($tags)) {
            $lines = preg_split("/\r\n|\n|\r/", $tags, -1, PREG_SPLIT_NO_EMPTY);
            if ($lines !== false) {
                $data['tags'] = $lines;
            }
        }

        $allMatchingTags = $this->input('all_matching_tags');
        if (is_string($allMatchingTags)) {
            $lines = preg_split("/\r\n|\n|\r/", $allMatchingTags, -1, PREG_SPLIT_NO_EMPTY);
            if ($lines !== false) {
                $data['all_matching_tags'] = $lines;
            }
        }

        $checklist = $this->input('checklist');

        if (is_string($checklist)) {
            $lines = preg_split("/\r\n|\n|\r/", $checklist, -1, PREG_SPLIT_NO_EMPTY);

            if ($lines !== false) {
                $data['checklist'] = $lines;
            }
        }

        $isInbox = $this->input('is_inbox');

        if (is_string($isInbox)) {
            $data['is_inbox'] = $isInbox === 'yes';
        }

        $isEvening = $this->input('evening');
        if (is_string($isEvening)) {
            $data['evening'] = $isEvening === 'yes';
        }

        $isLogged = $this->input('is_logged');
        if (is_string($isLogged)) {
            $data['is_logged'] = $isLogged === 'yes';
        }

        $startDate = $this->input('start_date');
        if (is_string($startDate)) {
            $data['start_date'] = Carbon::parse($startDate);
        }

        $reminderDate = $this->input('reminder_date');
        if (is_string($reminderDate)) {
            $data['reminder_at'] = Carbon::parse($reminderDate);
        }

        $deadlineDate = $this->input('deadline');
        if (is_string($deadlineDate)) {
            $data['deadline_at'] = Carbon::parse($deadlineDate);
        }

        $completionDate = $this->input('completion_date');
        if (is_string($completionDate)) {
            $data['completed_at'] = Carbon::parse($completionDate);
        }

        $modificationDate = $this->input('modification_date');
        if (is_string($modificationDate)) {
            $data['modification_date'] = Carbon::parse($modificationDate);
        }

        $creationDate = $this->input('creation_date');
        if (is_string($creationDate)) {
            $data['creation_date'] = Carbon::parse($creationDate);
        }

        if ($data !== []) {
            $this->merge($data);
        }
    }

    protected function failedValidation(Validator $validator)
    {
        if (! app()->runningUnitTests()) {
            dump($validator->errors());
        }
    }
}
