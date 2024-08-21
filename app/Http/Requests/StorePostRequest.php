<?php

namespace App\Http\Requests;

use App\Http\Enums\GroupUserStatus;
use App\Models\GroupUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StorePostRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static array $extensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'mp3', 'wav', 'mp4',
        'doc', 'docx', 'pdf', 'csv', 'xls', 'xlsx',
        'zip'
    ];


    public function rules(): array
    {
        return [
            //
            'body' => 'nullable | string',
            'preview' => 'nullable | array',
            'preview_url' => 'nullable | string',
            'attachments' => [
                'array',
                'max:50',
                function($attribute, $value, $fail){
                    // Custom rule to check the total size of all files
                    $totalSize = collect($value)->sum(function($file){
                        return $file->getSize();
                    });

                    if($totalSize > 1024 * 1024 * 1024){
                        $fail('The total size of all files must not exceed 1GB');
                    }
                }
            ],
            'attachments.*' => [
                'file',
                File::types(self::$extensions)
                ],
            'user_id' => 'numeric',
            'group_id' => ['nullable', 'exists:groups,id', function(string $attribute, mixed $value, \Closure $fail){
                $groupUser = GroupUser::where([
                    'user_id' => auth()->id(),
                    'group_id' => $value,
                    'status' => GroupUserStatus::APPROVED->value
                ])->exists();

                if(!$groupUser){
                    $fail('You do not have permission to create posts in this group');
                }
            }]
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->user()->id,
            'body' => $this->input('body') ?: ''
        ]);
    }

    public function messages()
    {
       return [
        'attachments.*.file' => 'Each attachment must be a file',
        'attachments.*.mimes' => 'Invalid file type for attachment.'
       ];
    }

}
