<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\MerchantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MerchantController extends Controller
{
    protected $merchantService;

    public function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    public function index()
    {
        Gate::authorize('admin');

        $result = $this->merchantService->getAllMerchants();

        return view('admin.merchants', [
            'merchants' => $result['data'],
            'message' => $result['message'],
        ]);
    }


    public function store(Request $request)
    {
        $result = $this->merchantService->createMerchant($request->only('name', 'address'));

        return redirect()->route('admin.merchants.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function destroy($id)
    {
        $result = $this->merchantService->deleteMerchant($id);

        return redirect()->route('admin.merchants.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function toggleStatus($id)
    {
        $result = $this->merchantService->toggleMerchantStatus($id);

        return redirect()->route('admin.merchants.index')->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }
}
