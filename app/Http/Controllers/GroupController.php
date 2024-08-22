<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Requests\InviteUserRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupUserResource;
use App\Http\Resources\PostAttachmentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\User;
use App\Notifications\GroupInvitationRequestApproved;
use App\Notifications\GroupIvitationAccepted;
use App\Notifications\GroupIvitationRequested;
use App\Notifications\GroupUserRoleChanged;
use App\Notifications\InvitationToGroupCreated;
use App\Notifications\UserRemovedFromGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function groupProfile(Request $request, Group $group)
    {
        //

        $group->load('currentUserGroup');

        $userId = auth()->id();

        if($group->hasApprovedUser($userId)){
            $posts = Post::postsForTimeline($userId, true)
                            ->where('group_id', $group->id)
                            ->paginate(5);
            $posts = PostResource::collection($posts);
        } else {
            $posts = null;

            return Inertia::render('Group/View', [
                'success' => session('success'),
                'group' => new GroupResource($group),
                'posts' => $posts,
                'users' => [],
                'requests' => []
            ]);
        }


        if($request->wantsJson()){
            return $posts;
        }

        $users = User::query()
                    ->select(['users.*', 'gu.role', 'gu.status', 'gu.group_id'])
                    ->join('group_users AS gu', 'gu.user_id', 'users.id')
                    ->orderBy('gu.role')
                    ->where('group_id', $group->id)
                    ->get();

        $requests = $group->pendingUsers()->orderBy('name')->get();

        $photos = PostAttachment::query()
                ->select('post_attachments.*')
                ->join('posts AS p', 'p.id', 'post_attachments.post_id')
                ->where('p.group_id', $group->id)
                ->where('mime', 'like', 'image/%')
                ->latest()
                ->get();

        return Inertia::render('Group/View', [
            'success' => session('success'),
            'group' => new GroupResource($group),
            'posts' => $posts,
            'users' => GroupUserResource::collection($users),
            'requests' => UserResource::collection($requests),
            'photos' => PostAttachmentResource::collection($photos)
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
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());

        return back()->with('success', 'The group information was updated successfully');
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

    public function approveRequest(Request $request, Group $group)
    {
        if(!$group->isAdmin(auth()->id())){
            return response('You do not have permission to perform this action', 403);
        }

        $data = $request->validate([
            'user_id' => 'required',
            'action' => 'required'
        ]);

        $groupUser = GroupUser::where([
            'user_id' => $data['user_id'],
            'group_id' => $group->id,
            'status' => GroupUserStatus::PENDING->value
        ])->first();

        if($groupUser){
            $approved = false;
            if($data['action'] === 'approve'){
                $approved = true;
                $groupUser->status = GroupUserStatus::APPROVED->value;
            }elseif($data['action'] === 'reject'){
                $groupUser->status = GroupUserStatus::REJECTED->value;
                // we can just delete the user instance if he or she is rejected
                // TODO
            }
            $groupUser->save();

            $user = $groupUser->user;

            $user->notify(new GroupInvitationRequestApproved($groupUser->group, $user, $approved));

            return back()->with('success', 'User "'.$user->name.'" was successfully '.($approved ? 'approved' : 'rejected'));
        }

        return back();

    }

    public function changeGroupRole(Request $request, Group $group)
    {
        if(!$group->isAdmin(auth()->id())){
            return response('You do not have permission to perform this action', 403);
        }

        $data = $request->validate([
            'user_id' => 'required',
            'role' => ['required', Rule::enum(GroupUserRole::class)]
        ]);

        $user_id = $data['user_id'];

        if($group->isOwner($user_id)){
            return response('You cannot change the role of the owner of the group', 403);
        }

        $groupUser = GroupUser::where([
            'user_id' => $user_id,
            'group_id' => $group->id,
        ])->first();

        if($groupUser){
            $groupUser->role = $data['role'];

            $groupUser->save();

            $groupUser->user->notify(new GroupUserRoleChanged($group, $data['role']));


            return back()->with('success', $groupUser->user->name . ' was made ' . $groupUser->role);
        }
        return back();
    }

    public function deleteUser(Request $request, Group $group)
    {
        if(!$group->isAdmin(auth()->id())){
            return response('You do not have permission to perform this action', 403);
        }

        $data = $request->validate([
            'user_id' => 'required'
        ]);

        $user_id = $data['user_id'];

        if($group->isOwner($user_id)){
            return response('You cannot remove yourself from the group', 403);
        }

        $groupUser = GroupUser::where([
            'user_id' => $user_id,
            'group_id' => $group->id,
        ])->first();

        if($groupUser){

            $user = $groupUser->user;

            $groupUser->delete();

            $user->notify(new UserRemovedFromGroup($group));


            return back()->with('success', $groupUser->user->name . ' was made ' . $groupUser->role);
        }
        return back();
    }
}
