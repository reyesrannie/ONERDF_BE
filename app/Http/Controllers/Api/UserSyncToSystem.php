<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditTrail;
use Illuminate\Http\Request;
use App\Models\PasswordManager;
use App\function\ResponseMessage;
use App\Services\SecureEncrypter;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class UserSyncToSystem extends Controller
{
    use ApiResponse;

    public function store(Request $request, SecureEncrypter $encrypter)
    {
        $user_login = Auth::id();
        $existingAccount = PasswordManager::where(
            "id_prefix",
            $request->id_prefix
        )
            ->where("id_no", $request->id_no)
            ->first();

        if ($existingAccount) {
            $passwordToUse = $encrypter->decrypt(
                $existingAccount->password_encrypted
            );
        } else {
            $passwordToUse = $request->username;

            PasswordManager::create([
                "id_prefix" => $request->id_prefix,
                "id_no" => $request->id_no,
                "password_encrypted" => $encrypter->encrypt($passwordToUse),
            ]);
        }

        $api = [
            "username" => $request->username,
            "password" => $passwordToUse,
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "middle_name" => $request->middle_name,
            "suffix" => $request->suffix,
            "id_prefix" => $request->id_prefix,
            "id_no" => $request->id_no,
        ];

        try {
            $response = Http::withOptions(["verify" => false])
                ->withHeaders(["api-key" => $request->endpoint["token"]])
                ->post($request->endpoint["url"], $api);

            if ($response->failed()) {
                $body = json_decode($response->body(), true);
                $message =
                    $body["message"] ?? ($response->body() ?? "Unknown error");

                AuditTrail::create([
                    "user_id" => $user_login,
                    "system_id" => $request->endpoint["id"],
                    "action" => "Unable to create user for {$api["id_prefix"]}-{$api["id_no"]}",
                    "module" => "User",
                    "details" => "Error from {$request->endpoint["name"]} ({$response->status()}): {$message}",
                ]);
            }
        } catch (ConnectionException $e) {
            AuditTrail::create([
                "user_id" => $user_login,
                "system_id" => $request->endpoint["id"],
                "action" => "Unable to create user for {$api["id_prefix"]}-{$api["id_no"]}",
                "module" => "User",
                "details" => "Connection error from {$request->endpoint["name"]}: {$e->getMessage()}",
            ]);
        } catch (\Exception $e) {
            AuditTrail::create([
                "user_id" => $user_login,
                "system_id" => $request->endpoint["id"],
                "action" => "Unable to create user for {$api["id_prefix"]}-{$api["id_no"]}",
                "module" => "User",
                "details" => "Unexpected error from {$request->endpoint["name"]}: {$e->getMessage()}",
            ]);
        }

        return $this->responseCreated(ResponseMessage::SYNC);
    }

    public function resetAllPassword(
        Request $request,
        SecureEncrypter $encrypter
    ) {
        $user_login = Auth::id();
        $userId = "/{$request->id_prefix}-{$request->id_no}";

        $existingAccount = PasswordManager::where(
            "id_prefix",
            $request->id_prefix
        )
            ->where("id_no", $request->id_no)
            ->first();

        if ($existingAccount) {
            $passwordToUse = $encrypter->decrypt(
                $existingAccount->password_encrypted
            );
        } else {
            $passwordToUse = $request->username;

            PasswordManager::create([
                "id_prefix" => $request->id_prefix,
                "id_no" => $request->id_no,
                "password_encrypted" => $encrypter->encrypt($passwordToUse),
            ]);
        }

        try {
            $response = Http::withOptions(["verify" => false])
                ->withHeaders(["api-key" => $request->endpoint["token"]])
                ->patch($request->endpoint["url"] . $userId);

            if ($response->failed()) {
                $body = json_decode($response->body(), true);
                $message =
                    $body["message"] ?? ($response->body() ?? "Unknown error");

                AuditTrail::create([
                    "user_id" => $user_login,
                    "system_id" => $request->endpoint["id"],
                    "action" => "Unable to reset password for {$userId}",
                    "module" => "User",
                    "details" => "Error from {$request->endpoint["name"]} ({$response->status()}): {$message}",
                ]);
            }
        } catch (ConnectionException $e) {
            AuditTrail::create([
                "user_id" => $user_login,
                "system_id" => $request->endpoint["id"],
                "action" => "Unable to reset password for {$userId}",
                "module" => "User",
                "details" => "Connection error from {$request->endpoint["name"]}: {$e->getMessage()}",
            ]);
        } catch (\Exception $e) {
            AuditTrail::create([
                "user_id" => $user_login,
                "system_id" => $request->endpoint["id"],
                "action" => "Unable to reset password for {$userId}",
                "module" => "User",
                "details" => "Unexpected error from {$request->endpoint["name"]}: {$e->getMessage()}",
            ]);
        }

        return $this->responseCreated(ResponseMessage::SYNC);
    }
    public function changeAllPassword(Request $request)
    {
        $user_login = Auth::id();

        $api = [
            "old_password" => $request->old_password,
            "password" => $request->password,
        ];
        $userId = "/{$request->id_prefix}-{$request->id_no}";

        try {
            $response = Http::withOptions(["verify" => false])
                ->withHeaders(["api-key" => $request->endpoint["token"]])
                ->patch($request->endpoint["url"] . $userId, $api);

            if ($response->failed()) {
                $body = json_decode($response->body(), true);
                $message =
                    $body["message"] ?? ($response->body() ?? "Unknown error");

                AuditTrail::create([
                    "user_id" => $user_login,
                    "system_id" => $request->endpoint["id"],
                    "action" => "Unable to change password for {$userId}",
                    "module" => "User",
                    "details" => "Error from {$request->endpoint["name"]} ({$response->status()}): {$message}",
                ]);
            }
        } catch (ConnectionException $e) {
            AuditTrail::create([
                "user_id" => $user_login,
                "system_id" => $request->endpoint["id"],
                "action" => "Unable to change password for {$userId}",
                "module" => "User",
                "details" => "Connection error from {$request->endpoint["name"]}: {$e->getMessage()}",
            ]);
        } catch (\Exception $e) {
            AuditTrail::create([
                "user_id" => $user_login,
                "system_id" => $request->endpoint["id"],
                "action" => "Unable to change password for {$userId}",
                "module" => "User",
                "details" => "Unexpected error from {$request->endpoint["name"]}: {$e->getMessage()}",
            ]);
        }

        return $this->responseCreated(ResponseMessage::SYNC);
    }

    public function systemLogin(Request $request, SecureEncrypter $encrypter)
    {
        $user_login = Auth::id();

        $existingAccount = PasswordManager::where(
            "id_prefix",
            $request->id_prefix
        )
            ->where("id_no", $request->id_no)
            ->first();

        if ($existingAccount) {
            $passwordToUse = $encrypter->decrypt(
                $existingAccount->password_encrypted
            );
        } else {
            $passwordToUse = $request->username;

            PasswordManager::create([
                "id_prefix" => $request->id_prefix,
                "id_no" => $request->id_no,
                "password_encrypted" => $encrypter->encrypt($passwordToUse),
            ]);
        }

        $api = [
            "username" => $request->username,
            "password" => $passwordToUse,
        ];

        $userId = "/{$request->id_prefix}-{$request->id_no}";

        try {
            $response = Http::withOptions(["verify" => false])
                ->withHeaders(["api-key" => $request->endpoint["token"]])
                ->post($request->endpoint["url"], $api);

            if ($response->failed()) {
                $body = json_decode($response->body(), true);
                $message =
                    $body["message"] ?? ($response->body() ?? "Unknown error");

                AuditTrail::create([
                    "user_id" => $user_login,
                    "system_id" => $request->endpoint["id"],
                    "action" => "Unable to login for {$userId}",
                    "module" => "Login",
                    "details" => "Error from {$request->endpoint["name"]} ({$response->status()}): {$message}",
                ]);
                return $this->responseNotFound("Login Failed");
            }

            return $this->responseSuccess("Login success", $response->json());
        } catch (ConnectionException $e) {
            AuditTrail::create([
                "user_id" => $user_login,
                "system_id" => $request->endpoint["id"],
                "action" => "Unable to login for {$userId}",
                "module" => "Login",
                "details" => "Connection error from {$request->endpoint["name"]}: {$e->getMessage()}",
            ]);
            return $this->responseNotFound("Login Failed");
        } catch (\Exception $e) {
            AuditTrail::create([
                "user_id" => $user_login,
                "system_id" => $request->endpoint["id"],
                "action" => "Unable to login for {$userId}",
                "module" => "Login",
                "details" => "Unexpected error from {$request->endpoint["name"]}: {$e->getMessage()}",
            ]);
            return $this->responseNotFound("Login Failed");
        }
    }

    public function testEncrypt(Request $request, SecureEncrypter $encrypter)
    {
        // return $request->id_prefix;

        $existingAccount = PasswordManager::where(
            "id_prefix",
            $request->id_prefix
        )
            ->where("id_no", $request->id_no)
            ->first();

        if ($existingAccount) {
            $passwordToUse = $encrypter->decrypt(
                $existingAccount->password_encrypted
            );
            return $passwordToUse;
        } else {
            return $this->responseNotFound("Login Failed");
        }
    }
}
