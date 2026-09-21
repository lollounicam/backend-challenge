<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = Client::create($request->validated());

        return response()->json(
            $client,
            Response::HTTP_CREATED
        );
    }
}
