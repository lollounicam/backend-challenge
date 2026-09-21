<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDebtCaseRequest;
use App\Models\DebtCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => [
                'sometimes',
                'string',
                Rule::in([
                    DebtCase::STATUS_NEW,
                    DebtCase::STATUS_IN_PROGRESS,
                    DebtCase::STATUS_CLOSED,
                ]),
            ],
        ]);

        $query = DebtCase::query();

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        return response()->json(
            $query->get(),
            Response::HTTP_OK,
        );
    }

    public function show(DebtCase $debtCase): JsonResponse
    {
        return response()->json(
            $debtCase,
            Response::HTTP_OK,
        );
    }
}
