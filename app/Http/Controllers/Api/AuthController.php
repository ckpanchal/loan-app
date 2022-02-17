<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;
use App\Services\UserService;
use Auth;
use JWTAuth;
use Validator;

class AuthController extends Controller
{
    /**
     * User service object
     *
     * @var object
     */
    protected $userService;

    /**
     * Initialize Auth Controller
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     * path="/api/register",
     * operationId="register",
     * tags={"Authentication"},
     * summary="User Register",
     * description="This is used to register loan app users.",
     *      @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name","email","password","password_confirmation"},
     *              @OA\Property(property="name", type="string", example="Chintan Panchal"),
     *              @OA\Property(property="email", type="string", example="admin@example.com"),
     *              @OA\Property(property="password", type="string",format="password", example="secret"),
     *              @OA\Property(property="password_confirmation", type="string",format="password", example="secret"), 
     *          ),
     *       ),
     *   ),
     *
     * @OA\Response(
     *    response=422,
     *    description="Validator Error"
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="Success"
     *     ),
     * )
     */
    public function register(RegisterRequest $request)
    {
        $role = Role::where('slug','=','user')->first();
        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => __('auth.not_found')
            ], 400);
        }
    
        $requestData = $request->all();
        $requestData['role_id'] = $role->id;
        $user = $this->userService->create($requestData);
        if ($user) {
            return response()->json([
                'status'  => true,
                'message' => __('auth.user_registered'),
                'user' => $user ?  new UserResource($user) : NULL
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('loan.unexpected_error')
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/login",
     * operationId="login",
     * tags={"Authentication"},
     * summary="User Login",
     * description="This is used to login loan app users.",
     *      @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", example="admin@example.com"),
     *              @OA\Property(property="password", type="string",format="password", example="secret"),
     *          ),
     *       ),
     *   ),
     *
     * @OA\Response(
     *    response=422,
     *    description="Validator Error"
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="Success"
     *     ),
     * )
     */
    public function login(Request $request)
    {
        if (!$token = auth()->attempt($request->input())) {
            return response()->json([
                'status' => false,
                'error' => __('auth.unauthorized')
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     * path="/api/logout",
     * operationId="logout",
     * tags={"Authentication"},
     * summary="Logout",
     * description="Logout app users",
     * security={ {"bearerAuth": {} }},
     *
     * @OA\Response(
     *    response=401,
     *    description="Token Error"
     * ),
     *
     * )
     */
    public function logout()
    {
        $user = JWTAuth::user();
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
            'status' => true,
            'message' => __('auth.user_logout')
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/refresh",
     * operationId="refresh",
     * tags={"Authentication"},
     * summary="Refresh",
     * description="This method is used to refresh authentication token",
     * security={ {"bearerAuth": {} }},
     *
     * @OA\Response(
     *    response=401,
     *    description="Token Error"
     * ),
     *
     * )
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * @OA\Get(
     * path="/api/profile",
     * operationId="profile",
     * tags={"Profile"},
     * summary="Refresh",
     * description="This method is used to get login user profile",
     * security={ {"bearerAuth": {} }},
     *
     * @OA\Response(
     *    response=401,
     *    description="Token Error"
     * ),
     *
     * )
     */
    public function profile()
    {
        $user = JWTAuth::user();
        $data = $user ?  new UserResource($user) : NULL;
        return response()->json([
            'status' => true,
            'data'   => $data
        ], 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status'       => true, 
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 200);
    }
}
