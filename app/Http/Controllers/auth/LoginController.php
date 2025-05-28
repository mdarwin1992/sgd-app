<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate the input data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Generate a unique key for rate limiting
        $rateLimiterKey = strtolower($request->input('email')) . '|' . $request->ip();

        // Check if the user has exceeded the attempt limit (5 attempts)
        if (RateLimiter::tooManyAttempts($rateLimiterKey, 5)) {
            $secondsRemaining = RateLimiter::availableIn($rateLimiterKey);

            return response()->json([
                'message' => "Too many attempts. Please try again in {$secondsRemaining} seconds."
            ], 429);
        }

        // Attempt to authenticate the user
        if (Auth::attempt($request->only('email', 'password'))) {
            // Clear the attempt counter if login is successful
            RateLimiter::clear($rateLimiterKey);
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            // Retrieve user roles and permissions
            $roles = $user->getRoleNames();

            // Check if the user has the 'Administrador' role
            $isAdmin = $roles->contains('ADMINISTRADOR');
            $isEntity = $roles->contains('EMPRESA');
            $isReader = $roles->contains('USUARIO');

            // Retrieve user office
            $office = Office::where('user_id', $user->id)->first(['id']);
            $officeId = $office ? $office->id : $user->id;

            // Retrieve user attributes (assuming the getUserAttributes method is defined in the controller)
            $attributes = $this->getUserAttributes($user);

            if ($isAdmin || $isEntity || $isReader) {
                $permissions = $user->getAllPermissions()->pluck('name');
            } else {
                $permissions = $user->modelPermissions()->pluck('name');
            }
            // Return a successful response with the token and user attributes
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'token_type' => 'Bearer',
                'roles' => $roles,
                'permissions' => $permissions,
                'entity_id' => $user->entity_id,
                'user_office_id' => $officeId,
                'attributes' => $attributes, // Call function to get custom user attributes
            ]);
        }

        // Increment the attempt counter if authentication fails
        RateLimiter::hit($rateLimiterKey);

        return response()->json([
            'message' => 'The provided credentials are incorrect.'
        ], 401);
    }

    public function logout(Request $request)
    {
        // Revoke all tokens for the current user
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function profile(Request $request)
    {
        // Return the authenticated user's profile data
        return response()->json($this->getUserAttributes($request->user()));
    }

    // Function to get specific user attributes
    private function getUserAttributes($user)
    {
        // Return the desired user attributes
        return $user->only(['id', 'email', 'name', 'created_at']); // Adjust the fields as needed
    }
}
