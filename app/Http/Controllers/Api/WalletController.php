<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    // Register Wallet
    public function register()
    {
        $user = Auth::user();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        return response()->json([
            'message' => 'Wallet berhasil dibuat',
            'data' => $wallet
        ], 201);
    }

    // Cek Saldo Wallet
    public function balance($id)
    {
        $user = Auth::user();

        $wallet = Wallet::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$wallet) {
            return response()->json([
                'message' => 'Wallet tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Saldo berhasil diambil',
            'data' => [
                'wallet_id' => $wallet->id,
                'balance' => $wallet->balance
            ]
        ], 200);
    }

    // Top Up Wallet
    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000',
        ]);

        $user = Auth::user();

        $result = DB::transaction(function () use ($user, $request) {
            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$wallet) {
                return null;
            }

            $balanceBefore = $wallet->balance;

            $wallet->balance += $request->amount;
            $wallet->save();

            Transaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'topup',
                'amount' => $request->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'idempotency_key' => (string) Str::uuid(),
                'description' => 'Top up saldo',
            ]);

            return [
                'amount' => $request->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
            ];
        });

        if ($result === null) {
            return response()->json([
                'message' => 'Wallet tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Top up berhasil',
            'data' => $result
        ], 200);
    }

    // Transfer antar-wallet
    public function transfer(Request $request)
    {
        $request->validate([
            'to_wallet_id' => 'required|integer|exists:wallets,id',
            'amount' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        $result = DB::transaction(function () use ($user, $request) {
            $senderWalletId = Wallet::where('user_id', $user->id)->value('id');

            if (!$senderWalletId) {
                return 'invalid';
            }

            $wallets = Wallet::whereIn('id', [
                $senderWalletId,
                $request->to_wallet_id
            ])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $sender = $wallets->firstWhere('id', $senderWalletId);
            $receiver = $wallets->firstWhere('id', $request->to_wallet_id);

            if (!$sender || !$receiver || $sender->id === $receiver->id) {
                return 'invalid';
            }

            if ($sender->balance < $request->amount) {
                return 'insufficient';
            }

            $senderBefore = $sender->balance;
            $receiverBefore = $receiver->balance;

            $sender->decrement('balance', $request->amount);
            $receiver->increment('balance', $request->amount);

            Transaction::create([
                'wallet_id' => $sender->id,
                'related_wallet_id' => $receiver->id,
                'type' => 'transfer',
                'amount' => $request->amount,
                'balance_before' => $senderBefore,
                'balance_after' => $senderBefore - $request->amount,
                'related_balance_before' => $receiverBefore,
                'related_balance_after' => $receiverBefore + $request->amount,
                'idempotency_key' => (string) Str::uuid(),
                'description' => 'Transfer saldo',
            ]);

            return 'success';
        });

        if ($result === 'invalid') {
            return response()->json([
                'message' => 'Wallet tujuan tidak valid'
            ], 422);
        }

        if ($result === 'insufficient') {
            return response()->json([
                'message' => 'Saldo tidak mencukupi'
            ], 422);
        }

        return response()->json([
            'message' => 'Transfer berhasil'
        ], 200);
    }
}
