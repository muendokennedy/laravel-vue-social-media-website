<?php

namespace App\Http\Requests;

use App\Http\Enums\GroupUserStatus;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class InviteUserRequest extends FormRequest
{
    public ?User $user = null;
    public Group $group;
    public ?GroupUser $groupUser = null;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\Group $group **/

        $this->group = $this->route('group');

        return $this->group->isAdmin(auth()->id());

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //

            'email' => ['required', function(string $attribute, mixed $value, \Closure $fail){
                $this->user = User::where('email', $value)->orWhere('username', $value)->first();

                if(!$this->user){
                    $fail('This user does not exist');
                }

                $this->groupUser = GroupUser::where([
                    'user_id' => $this->user->id,
                    'group_id' => $this->group->id
                ])->first();

                if($this->groupUser && $this->groupUser->status === GroupUserStatus::APPROVED->value){
                    $fail('This user is already joined to the group');
                }
            }]
         ];
    }
}
