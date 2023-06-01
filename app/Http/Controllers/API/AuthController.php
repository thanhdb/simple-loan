<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Repositories\User\UserRepositoryInterface;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Sanctum token
     * @var string
     */
    private $token = '';

    /**
     * UserRepository
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->token = config('auth.sanctum_token');
        $this->userRepository = $userRepository;
    }

    /**
     * User Register
     *
     * @param  RegisterRequest $request
     * @param  User $user
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, User $user)
    {
        // get validated request data
        $requestData = $request->validated();

        //create user
        $dataCreateUser = [
            'uuid' => Str::orderedUuid(),
            'name' => $requestData['name'],
            'email' => $requestData['email'],
            'password' => bcrypt($requestData['password']),
        ];

        $user = $this->userRepository->create($dataCreateUser);

        // assign role for user
        if ($request->get('role') === 'admin') {
            $user->assignRole('admin');
        } else {
            $user->assignRole('customer');
        }

        $data = [
            'user' => $user,
            'token' => $user->createToken($this->token)->plainTextToken
        ];
        return $this->successResponse($data, 'User created successfully', Response::HTTP_CREATED);
    }

    /**
     * User Login
     *
     * @param  LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $requestData = $request->validated();

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->errorResponse(
                'Email & Password does not match. Please check and try again.',
                Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userRepository->findByEmail($requestData['email']);

        $data = [
            'user' => $user,
            'token' => $user->createToken($this->token)->plainTextToken
        ];
        return $this->successResponse($data, 'User logged in successfully');
    }

    /**
     * User Logout (Revoke the token)
     * Receive token bearer in header request
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->successResponse([], 'User Logged Out Successfully');
    }
}
