<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotifyMeRequest;
use App\Services\NotifyMeService;

class NotifyMeController extends Controller
{
    protected $notifyMeService;

    public function __construct(NotifyMeService $notifyMeService)
    {
        $this->notifyMeService = $notifyMeService;
    }

    public function store(StoreNotifyMeRequest $request)
    {
        $this->notifyMeService->store(
            $request->validated(),
            auth()->id()
        );

        return response()->json([
            'message' => 'You will be notified when the product is back in stock.'
        ]);
    }

    public function adminDemandReport()
    {
        $topDemand = $this->notifyMeService
            ->getAdminDemandReport(auth()->id());

        return view('admin.dashboard', compact('topDemand'));
    }
}