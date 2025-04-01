<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentAccountController extends Controller
{
    /**
     * List Payments
     */
    public function index($id)
    {
        $payments = PaymentAccount::where('group_id', $id)->latest()->get();

        $data = [];
        foreach ($payments as $item) {
            $data[] = $item;
        }

        return response()->json(
            ['data' => $data],
            200
        );
    }

    /**
     * Create New Payment
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'group_id' => 'required',
            'account_name' => 'required',
            'bank_name' => 'required',
            'bank_number' => 'required',
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                400
            );
        }

        PaymentAccount::create([
            'group_id' => $request->group_id,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'bank_number' => $request->bank_number,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Akun pembayaran berhasil ditambahkan.",
        ], 200);
    }

    /**
     * Update Payment
     */
    public function update(Request $request, string $id)
    {
        $validate = Validator::make($request->all(), [
            'account_name' => 'required',
            'bank_name' => 'required',
            'bank_number' => 'required',
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                400
            );
        }

        $payment = PaymentAccount::find($id);

        if (!@$payment) {
            return response()->json([
                "message" => "Akun pembayaran tidak ditemukan"
            ], 404);
        }

        $payment->account_name = $request->account_name;
        $payment->bank_name = $request->bank_name;
        $payment->bank_number = $request->bank_number;
        $payment->save();

        return response()->json([
            "status" => "success",
            "message" => "Akun pembayaran berhasil diubah.",
        ], 200);
    }

    /**
     * Remove Payment
     */
    public function destroy(string $id)
    {
        $payment = PaymentAccount::find($id);

        if (!@$payment) {
            return response()->json([
                "message" => "Akun pembayaran tidak ditemukan"
            ], 404);
        }

        $payment->delete();

        return response()->json([
            "status" => "success",
            "message" => "Akun pembayaran berhasil dihapus.",
        ], 200);
    }
}
