<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Requests\InviteUserRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\GroupUser;
use App\Notifications\GroupIvitationAccepted;
use App\Notifications\GroupIvitationRequested;
use App\Notifications\InvitationToGroupCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function groupProfile(Group $group): Response
    {
        //

        $group->load('currentUserGroup');

        return Inertia::render('Group/View', [
            'success' => session('success'),
            'group' => new GroupResource($group)
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        //

        $data = $request->validated();

        $data['user_id'] = auth()->id();
        $data['deleted_by'] = $request->user()->id;

        $group = Group::create($data);

        $groupUserData = [
            'status' => GroupUserStatus::APPROVED->value,
            'role' => GroupUserRole::ADMIN->value,
            'user_id' => auth()->id(),
            'group_id' => $group->id,
            'created_by' => auth()->id()
        ];

        $groupUser = GroupUser::create($groupUserData);

        $group->status = $groupUser['status'];
        $group->role = $groupUser['role'];

        return response()->json(new GroupResource($group));
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        //
    }

    public function updateImages(Request $request, Group $group)
    {

        if(!$group->isAdmin(auth()->id())){
            return response('You do not have permission to perform this action', 403);
        }

        $data = $request->validate([
            'cover' => ['nullable', 'image'],
            'thumbnail' => ['nullable', 'image']
        ]);


        $thumbnail = $data['thumbnail'] ?? null;
        $cover = $data['cover'] ?? null;
        $success = null;

        if($cover){
            if($group->cover_path){
                Storage::disk('public')->delete($group->cover_path);
            }
            $path = $cover->store('covers/group-' . $group->id, 'public');
            $group->update(['cover_path' => $path]);

            $success = 'Your cover image was updated';
        }
        if($thumbnail){
            if($group->thumbnail_path){
                Storage::disk('public')->delete($group->thumbnail_path);
            }
            $path = $thumbnail->store('thumbnails/group-' . $group->id, 'public');
            $group->update(['thumbnail_path' => $path]);

            $success = 'Your thumbnail image was updated';
        }

        return back()->with('success', $success);

    }

    public function inviteUser(InviteUserRequest $request, Group $group)
    {

        $data = $request->validated();

        $user = $request->user;

        $groupUser = $request->groupUser;

        if($groupUser){
            $groupUser->delete();
        }

        $hours = 24;
        $token = Str::random(256);

        GroupUser::create([
            'status' => GroupUserStatus::PENDING->value,
            'role' => GroupUserRole::USER->value,
            'token' => $token,
            'token_expire_date' =>  Carbon::now()->addHours($hours),
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => auth()->id(),
        ]);

        $user->notify(new InvitationToGroupCreated($group, $hours, $token));

        return back()->with('success', 'The user was invited to join the group');
    }

    public function confirmInvitation(string $token)
    {
       $groupUser = GroupUser::where('token', $token)->first();

       $errorTitle = '';

       if(!$groupUser){
        $errorTitle = 'The link is not valid';
       } elseif($groupUser->token_used || $groupUser->status === GroupUserStatus::APPROVED->value){
        $errorTitle = 'The link is already used';
       } elseif($groupUser->token_expire_date < Carbon::now()){
        $errorTitle = 'The link is already expired';
       }

       if($errorTitle){
           return Inertia::render('Error', compact('errorTitle'));
       }

       $groupUser->status = GroupUserStatus::APPROVED->value;
       $groupUser->token_used = Carbon::now();
       $groupUser->save();

       $adminUser = $groupUser->adminUser;

       $adminUser->notify(new GroupIvitationAccepted($groupUser->group, $groupUser->user));

       return to_route('group.profile', $groupUser->group)->with('success', 'You accepted to join to "'.$groupUser->group->name.'" group');
    }

    public function joinGroup(Group $group)
    {
        $user = \request()->user();

        $status = GroupUserStatus::APPROVED->value;

        $successMessage = 'You have successfully joined to the group "'.$group->name.'"';

        if(!$group->auto_approval){

            $status = GroupUserStatus::PENDING->value;

            // Send an email

            Notification::send($group->adminUsers, new GroupIvitationRequested($group, $user));

            $successMessage = 'Your request to join "'.$group->name.'" has been received. You will be notified once you are approved to the group';

        }

        GroupUser::create([
            'status' => $status,
            'role' => GroupUserRole::USER->value,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => $user->id,
        ]);

        return back()->with('success', $successMessage);
    }
}
