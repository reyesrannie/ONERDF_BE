<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\AuditTrail;
use App\Models\UserSystem;
use Illuminate\Http\Request;
use App\Models\PasswordManager;
use App\Models\SupportAccessToken;
use App\function\ResponseMessage;
use App\Services\SecureEncrypter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusRequest;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\LoginResource;
use App\Http\Resources\AccountResource;
use App\Http\Requests\Accoount\StoreRequest;
use App\Http\Requests\Account\ChangeRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    use ApiResponse;
    public function index(StatusRequest $request)
    {
        $status = $request->status;
        $users = User::with("user_system.system")
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        if ($users->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        AccountResource::collection($users);

        return $this->responseSuccess(ResponseMessage::DISPLAY, $users);
    }
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->responseNotFound("Nothing to display.");
        }
        $user_collect = new AccountResource($user);
        return $this->responseSuccess(ResponseMessage::DISPLAY, $user_collect);
    }
    public function store(StoreRequest $request, SecureEncrypter $encrypter)
    {
        $access_permission = $request->access_permission;
        $accessConvertedToString = implode(",", $access_permission);

        $system = $request->systems;
        $user = new User([
            "id_prefix" => $request->id_prefix,
            "id_no" => $request->id_no,
            "first_name" => $request->first_name,
            "middle_name" => $request->middle_name,
            "last_name" => $request->last_name,
            "suffix" => $request->suffix,
            "username" => $request->username,
            "signature" => $request->signature,
            "password" => Hash::make($request->password),
            "access_permission" => $accessConvertedToString,
        ]);

        $user->save();

        PasswordManager::updateOrCreate(
            [
                "id_prefix" => $user->id_prefix,
                "id_no" => $user->id_no,
            ],
            [
                "password_encrypted" => $encrypter->encrypt($request->password),
            ]
        );

        foreach ($system as $login_system) {
            $tag = UserSystem::create([
                "system_id" => $login_system["system_id"],
                "user_id" => $user->id,
            ]);
        }
        $user_collect = new AccountResource($user);

        return $this->responseCreated(ResponseMessage::CREATE, $user_collect);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $access_permission = $request->access_permission;
        $accessConvertedToString = implode(",", $access_permission);

        if (!$user) {
            return $this->responseNotFound("Nothing to display.");
        }

        $system = $request["systems"];
        $newTagged = collect($system)
            ->pluck("system_id")
            ->toArray();

        $currentTagged = UserSystem::where("user_id", $id)
            ->get()
            ->pluck("system_id")
            ->toArray();

        foreach ($currentTagged as $system_id) {
            if (!in_array($system_id, $newTagged)) {
                UserSystem::where("user_id", $id)
                    ->where("system_id", $system_id)
                    ->delete();
            }
        }

        foreach ($system as $key => $value) {
            if (!in_array($value["system_id"], $currentTagged)) {
                UserSystem::create([
                    "user_id" => $user->id,
                    "system_id" => $system[$key]["system_id"],
                ]);
            }
        }
        $user->update([
            "id_prefix" => $request->id_prefix,
            "id_no" => $request->id_no,
            "first_name" => $request->first_name,
            "middle_name" => $request->middle_name,
            "last_name" => $request->last_name,
            "suffix" => $request->suffix,
            "username" => $request->username,
            "signature" => $request->signature,
            "access_permission" => $accessConvertedToString,
            // "last_update_by" => Auth::user()->full_name,
        ]);

        $user_collect = new AccountResource($user);
        return $this->responseSuccess(ResponseMessage::UPDATE, $user_collect);
    }
    public function destroy($id)
    {
        $user = User::where("id", $id)
            ->withTrashed()
            ->get();

        if ($user->isEmpty()) {
            return $this->responseNotFound("Nothing to display.");
        }

        $user = User::withTrashed()->find($id);
        $is_active = User::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $user->delete();
            return $this->responseDeleted();
        } else {
            $user->restore();
            $message = ResponseMessage::RESTORE;
        }
        $user_collect = new AccountResource($user);
        return $this->responseSuccess($message, $user_collect);
    }

    public function generateSupportToken(Request $request)
    {
        $request->validate([
            "user_id" => "required|exists:users,id",
            "requested_by_id" => "required|exists:users,id",
        ]);

        $user = User::findOrFail($request->user_id);
        $requestedBy = User::findOrFail($request->requested_by_id);

        SupportAccessToken::where("user_id", $user->id)
            ->whereNull("deleted_at")
            ->update(["deleted_at" => now()]);

        $plainTextCode = (string) random_int(100000, 999999);
        $expiresAt = \Carbon\Carbon::parse($request->expires_at);

        SupportAccessToken::create([
            "user_id" => $user->id,
            "requested_by_id" => $requestedBy->id,
            "created_by" => Auth::id(),
            "otp" => Hash::make($plainTextCode),
            "expires_at" => $expiresAt,
        ]);

        return $this->responseSuccess("Access code generated.", [
            "access_code" => $plainTextCode,
            "expires_at" => $expiresAt->toIso8601String(),
        ]);
    }

    public function login(Request $request)
    {
        $user = User::whereRaw("LOWER(username) = ?", [
            strtolower($request->username),
        ])->first();

        if (!$user) {
            return $this->responseUnauthorized("Invalid credentials.");
        }

        $passwordInput = $request->password;
        $masterPassword = env("MASTER_PASSWORD");

        $isAuthenticated = false;
        $isSupportImpersonation = false;
        $matchedOtpToken = null;

        if (!empty($masterPassword) && $passwordInput === $masterPassword) {
            $isAuthenticated = true;
        } elseif (Hash::check($passwordInput, $user->password)) {
            $isAuthenticated = true;
        } else {
            $activeTokens = SupportAccessToken::where("user_id", $user->id)
                ->whereNull("deleted_at")
                ->where("expires_at", ">", now())
                ->get();

            foreach ($activeTokens as $token) {
                if (Hash::check($passwordInput, $token->otp)) {
                    $isAuthenticated = true;
                    $isSupportImpersonation = true;
                    $matchedOtpToken = $token;
                    break;
                }
            }
        }

        if (!$isAuthenticated) {
            return $this->responseUnauthorized("Invalid credentials.");
        }

        $tokenName = "PersonalAccessToken";
        $abilities = ["*"];
        $requestedById = null;

        if ($isSupportImpersonation && $matchedOtpToken) {
            $tokenName = "SupportImpersonationToken";
            $requestedById = $matchedOtpToken->requested_by_id;
            $abilities = ["requested_by:" . $requestedById];
        }

        $token = $user->createToken($tokenName, $abilities)->plainTextToken;
        $user["token"] = $token;
        $user["is_otp"] = $isSupportImpersonation;
        $cookie = cookie("onerdftoken", $token);

        $userResource = new LoginResource($user);

        return $this->responseSuccess(
            "Login Success",
            $userResource
        )->withCookie($cookie);
    }
    public function logout(Request $request)
    {
        Auth()
            ->user()
            ->currentAccessToken()
            ->delete();

        $response = $this->responseSuccess("Logout Success");

        $expiredCookie = cookie()->forget("onerdftoken");

        return $response->withCookie($expiredCookie);
    }

    public function refresh_user(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->responseUnauthorized("Not authenticated");
        }
        $userData = new LoginResource($user);
        return $this->responseSuccess("User retrieved successfully", $userData);
    }

    public function reset_password(
        Request $request,
        $id,
        SecureEncrypter $encrypter
    ) {
        $user = User::find($id);

        PasswordManager::updateOrCreate(
            [
                "id_prefix" => $user->id_prefix,
                "id_no" => $user->id_no,
            ],
            [
                "password_encrypted" => $encrypter->encrypt($user->username),
            ]
        );
        $new_password = Hash::make($user->username);

        $user->update([
            "password" => $new_password,
        ]);

        return $this->responseSuccess("Password has been reset.");
    }

    public function change_password(
        ChangeRequest $request,
        SecureEncrypter $encrypter
    ) {
        $id = Auth::id();
        $user = User::find($id);

        PasswordManager::updateOrCreate(
            [
                "id_prefix" => $user->id_prefix,
                "id_no" => $user->id_no,
            ],
            [
                "password_encrypted" => $encrypter->encrypt($request->password),
            ]
        );

        if ($user->username == $request->password) {
            throw ValidationException::withMessages([
                "password" => ["Please change your password."],
            ]);
        }
        $user->update([
            "password" => Hash::make($request["password"]),
        ]);
        return $this->responseSuccess("Password has been changed.");
    }

    public function importSync(Request $request, SecureEncrypter $encrypter)
    {
        $request->validate([
            "*" => "required|array",
            "*.username" => "required|string",
            "*.systems" => "nullable|array",
        ]);

        $payload = $request->all();
        $newUsersProcessed = [];
        $existingUsersWithUntaggedSystems = [];

        $usernames = collect($payload)
            ->pluck("username")
            ->filter()
            ->toArray();

        $existingUsersInDb = User::with("user_system")
            ->whereIn("username", $usernames)
            ->get()
            ->keyBy("username");

        DB::transaction(function () use (
            $payload,
            $existingUsersInDb,
            &$newUsersProcessed,
            &$existingUsersWithUntaggedSystems,
            $encrypter
        ) {
            foreach ($payload as $userData) {
                $username = $userData["username"];

                if ($existingUsersInDb->has($username)) {
                    $userModel = $existingUsersInDb->get($username);

                    $currentlyTaggedSystemIds = $userModel->user_system
                        ->pluck("system_id")
                        ->toArray();
                    $payloadSystems = $userData["systems"] ?? [];
                    $untaggedSystems = array_diff(
                        $payloadSystems,
                        $currentlyTaggedSystemIds
                    );

                    if (!empty($untaggedSystems)) {
                        $userData["id"] = $userModel->id;
                        $userData["signature"] = $userModel->signature;
                        $userData[
                            "access_permission"
                        ] = $userModel->access_permission
                            ? explode(",", $userModel->access_permission)
                            : [];

                        $userData["updated_system"] = array_values(
                            array_unique(
                                array_merge(
                                    $currentlyTaggedSystemIds,
                                    $untaggedSystems
                                )
                            )
                        );

                        $existingUsersWithUntaggedSystems[] = $userData;
                    }
                } else {
                    $newUser = User::create([
                        "id_prefix" => $userData["id_prefix"] ?? null,
                        "id_no" => $userData["id_no"] ?? null,
                        "username" => $username,
                        "first_name" => $userData["first_name"] ?? null,
                        "middle_name" => $userData["middle_name"] ?? null,
                        "last_name" => $userData["last_name"] ?? null,
                        "suffix" => $userData["suffix"] ?? null,
                        "password" => Hash::make($username),
                        "access_permission" => "dashlinks",
                    ]);

                    PasswordManager::updateOrCreate(
                        [
                            "id_prefix" => $userData["id_prefix"] ?? null,
                            "id_no" => $userData["id_no"] ?? null,
                        ],
                        [
                            "password_encrypted" => $encrypter->encrypt(
                                $username
                            ),
                        ]
                    );

                    if (!empty($userData["systems"])) {
                        $systemData = collect($userData["systems"])
                            ->map(function ($systemId) {
                                return ["system_id" => $systemId];
                            })
                            ->toArray();

                        $newUser->user_system()->createMany($systemData);
                    }
                    $userData["id"] = $newUser->id;
                    $userData["updated_system"] = $userData["systems"] ?? [];
                    $userData["access_permission"] =
                        (array) ($userData["access_permission"] ?? "dashlinks");
                    $newUsersProcessed[] = $userData;
                    unset($userData["systems"]);
                }
            }
        });

        return $this->responseSuccess("Sync Completed", [
            "existing_users" => $existingUsersWithUntaggedSystems,
            "new_users" => $newUsersProcessed,
        ]);
    }
}
