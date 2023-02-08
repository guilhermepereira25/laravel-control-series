<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthApiController
{
    /**
     * Login via api, retorna o token gerado no momento do login
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['success' => 'false'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('token', expiresAt: new \DateTime('now +1 day'));

        return response()->json(['success' => 'true', 'token' => $token->plainTextToken], 200);
    }

    public function user(Request $request): JsonResponse
    {
        if ($request->has('email')) {
            $user = User::where('email', $request->email)->first();

            if (is_null($user)) {
                return response()->json(['success' => 'false', 'user' =>  'null'], 404);
            }

            return response()->json(['success' => 'true', 'user' => $user], 200);
        }

        return response()->json(['success' => 'false', 'message' =>  'invalid request'], 403);
    }

    public function verifyUserToken($token): bool
    {
        $canToken = DB::table('personal_access_tokens')
            ->where('token', $token)
            ->whereDate('expires_at', '>', new \DateTime('now'))
            ->get();

        $isNotEmpty = $canToken->isNotEmpty();

        if ($isNotEmpty) {
            $this->updateLastUsedToken($canToken['0']->id);
        }

        return $isNotEmpty;
    }

    private function updateLastUsedToken(int $id): void
    {
        DB::table('personal_access_tokens')
            ->where('id', $id)
            ->update(['last_used_at' => new \DateTime('now')]);
    }
}
