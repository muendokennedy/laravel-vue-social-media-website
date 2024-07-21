<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
}
