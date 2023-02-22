<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreRequest;
use App\Http\Resources\Organization\OrganizationResource;
use App\Models\Organization;

class OrganizationController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => new OrganizationResource(Organization::firstOrFail())
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->authorize('create', new Organization());
        $data = $request->validated();

        if (Organization::count()) return response()->json([
            'errors' => [
                'title' => 'Conflict',
                'detail' => 'There can be only one organization',
                'status' => 409
            ]
        ], 409);

        return response()->json([
            'data' => new OrganizationResource(Organization::create($data))
        ]);
    }

    public function update(StoreRequest $request, Organization $organization)
    {
        $this->authorize('update', new Organization());
        $data = $request->validated();
        $organization->updateOrFail($data);

        return response()->json([
            'data' => new OrganizationResource($organization)
        ]);
    }

    public function destroy(Organization $organization)
    {
        $this->authorize('delete', new Organization());

       $organization->deleteOrFail();

        return response()->json([
            'messages' => [
                'title' => "User with $organization->id id deleted",
                'detail' => "User with $organization->id id deleted",
                'status' => 200
            ]
        ]);
    }
}
