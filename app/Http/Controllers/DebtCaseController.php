<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDebtCaseRequest;
use App\Models\DebtCase;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DebtCaseController extends Controller
{
    public function store(StoreDebtCaseRequest $request): JsonResponse
    {
        $debtCase = DebtCase::create($request->validated());

        $debtCase->refresh();

        return response()->json(
            $debtCase,
            Response::HTTP_CREATED
        );
    }
}
