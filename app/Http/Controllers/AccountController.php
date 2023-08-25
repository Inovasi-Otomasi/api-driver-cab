<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Throwable;

class AccountController extends Controller
{
    //
    public function index()
    {
        $account = User::with('roles')->get();
        return response()->json($account, 200);
    }
    public function show(User $account)
    {
        $account->load('roles');
        return response()->json($account, 200);
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $validatedData["password"] = Hash::make($validatedData["password"]);
            User::Create($validatedData);
            return response()->json([
                "status" => "success",
                "message" => "User added"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot add user"
            ], 400);
        }
    }
    public function updatePassword(Request $request, User $account)
    {
        # Validation
        // dd($request);
        try {
            $change_password = $request->validate([
                'old_password' => 'required',
                'new_password' => 'required',
            ]);
            #Match The Old Password
            if (!Hash::check($request->old_password, $account->password)) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Wrong password'
                ], 400);
            }
            // #Update the new Password

            User::whereId($account->id)->update([
                'password' => Hash::make($request->new_password)
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Password account ' . $account->id . ' has changed'
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to change password'
            ], 400);
        }


        // return back()->with("status", "Password changed successfully!");
    }

    public function destroy(User $account)
    {
        //
        try {
            User::where('id', $account->id)->delete();
            return response()->json([
                "status" => "success",
                "message" => "Delete id " . $account->id . " success"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "status" => "failed",
                "message" => "Cannot delete account"
            ], 400);
        }
    }

    public function assignRole(Request $request, User $account)
    {
        # Validation
        try {
            $change_role = $request->validate([
                'role' => 'required',
            ]);
            $current_role = $account->getRoleNames();
            $account->syncRoles([]);
            $account->assignRole($change_role['role']);
            return response()->json([
                'status' => 'success',
                'message' => 'Role account ' . $account->id . ' has changed to ' . $change_role['role']
            ], 400);
        } catch (Throwable $e) {
            $account->syncRoles([]);
            $account->assignRole($current_role);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to change role, reverted to role ' . $current_role
            ], 400);
        }
    }

    public function roles()
    {
        return Role::all();
    }
}
