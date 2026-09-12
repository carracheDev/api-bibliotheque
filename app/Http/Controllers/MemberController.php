<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return MemberResource::collection(
            Member::query()->withCount('activeLoans')->orderBy('name')->paginate(15)
        );
    }

    public function store(StoreMemberRequest $request): MemberResource|JsonResponse
    {
        $resource = new MemberResource(Member::create([
            ...$request->validated(),
            'registered_at' => $request->validated('registered_at') ?? now()->toDateString(),
        ]));

        return $resource->response()->setStatusCode(201);
    }

    public function show(Member $member): MemberResource
    {
        return new MemberResource($member->loadCount('activeLoans'));
    }

    public function update(UpdateMemberRequest $request, Member $member): MemberResource
    {
        $member->update($request->validated());

        return new MemberResource($member->fresh()->loadCount('activeLoans'));
    }

    public function destroy(Member $member): \Illuminate\Http\Response
    {
        abort_if($member->activeLoans()->exists(), 409, 'Un membre ayant un emprunt actif ne peut pas être supprimé.');
        $member->delete();

        return response()->noContent();
    }
}