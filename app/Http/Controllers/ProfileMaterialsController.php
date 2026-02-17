<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\AddMaterialPostRequest;
use App\Http\Requests\Profile\MaterialDeleteRequest;
use App\Http\Requests\Profile\MaterialPatchRequest;
use App\Http\Requests\Profile\MaterialsGetRequest;
use App\Services\ProfileMaterialsService;
use Illuminate\Http\Request;

class ProfileMaterialsController extends Controller
{
    protected $profileMaterialsService;

    public function __construct(ProfileMaterialsService $profileMaterialsService)
    {
        $this->profileMaterialsService = $profileMaterialsService;
    }

    public function addMaterial(AddMaterialPostRequest $request)
    {
        $response = $this->profileMaterialsService->addMaterial($request);

        return response()->json([
            'success' => true,
            ...$response
        ]);
    }

    public function delMaterial(MaterialDeleteRequest $request)
    {
        $response = $this->profileMaterialsService->delMaterial($request);

        return response()->json([
            'success' => true,
            ...$response
        ]);
    }

    public function updateMaterial(MaterialPatchRequest $request)
    {
        $response = $this->profileMaterialsService->updateMaterial($request);

        return response()->json([
            'success' => true,
            ...$response
        ]);
    }

    public function show(MaterialsGetRequest $request)
    {
        $response = $this->profileMaterialsService->show($request);

        return response()->json([
            'success' => true,
            'teacher_materials' => $response
        ]);
    }
}
