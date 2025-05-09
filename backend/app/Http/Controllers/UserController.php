<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Traits\ResponseTrait;
use App\Enums\ResponseCode;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $items = DB::table('users')
                ->select('id', 'name', 'email', 'code', 'age')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            Log::info('Users list retrieved successfully');

            return $this->successResponse(
                'Users retrieved successfully',
                [
                    'items' => $items->items(),
                    'meta' => [
                        'current_page' => $items->currentPage(),
                        'last_page' => $items->lastPage(),
                        'per_page' => $items->perPage(),
                        'total' => $items->total(),
                    ],
                ],
                ResponseCode::SUCCESS
            );
        } catch (\Exception $e) {
            Log::error('Failed to retrieve users list: ' . $e->getMessage());
            return $this->errorResponse(
                'Failed to retrieve users',
                null,
                ResponseCode::INTERNAL_ERROR
            );
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['code'] = Str::random(6);
            $user = User::create($validatedData);
            Log::info('User created successfully', ['id' => $user->id]);

            return $this->successResponse(
                'User created successfully',
                null,
                ResponseCode::CREATED
            );
        } catch (ValidationException $e) {
            Log::warning('Validation failed while creating user', ['errors' => $e->errors()]);
            return $this->errorResponse(
                'Validation failed',
                $e->errors(),
                ResponseCode::VALIDATION_ERROR
            );
        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            return $this->errorResponse(
                'An error occurred while creating user',
                null,
                ResponseCode::INTERNAL_ERROR
            );
        }
    }

    public function show($id)
    {
        try {
            $user = User::find($id);
            
            if (is_null($user)) {
                Log::warning('User not found', ['id' => $id]);
                return $this->errorResponse(
                    'User not found',
                    null,
                    ResponseCode::NOT_FOUND
                );
            }

            Log::info('User retrieved successfully', ['id' => $id]);
            return $this->successResponse(
                'User retrieved successfully',
                $user,
                ResponseCode::SUCCESS
            );
        } catch (\Exception $e) {
            Log::error('Failed to retrieve user: ' . $e->getMessage(), ['id' => $id]);
            return $this->errorResponse(
                'An error occurred while retrieving user',
                null,
                ResponseCode::INTERNAL_ERROR
            );
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($request->validated());
            Log::info('User updated successfully', ['id' => $id]);

            return $this->successResponse(
                'User updated successfully',
                null,
                ResponseCode::SUCCESS
            );
        } catch (ValidationException $e) {
            Log::warning('Validation failed while updating user', ['id' => $id, 'errors' => $e->errors()]);
            return $this->errorResponse(
                'Validation failed',
                $e->errors(),
                ResponseCode::VALIDATION_ERROR
            );
        } catch (\Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage(), ['id' => $id]);
            return $this->errorResponse(
                'An error occurred while updating user',
                null,
                ResponseCode::INTERNAL_ERROR
            );
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find($id);
            
            if (is_null($user)) {
                Log::warning('User not found', ['id' => $id]);
                return $this->errorResponse(
                    'User not found',
                    null,
                    ResponseCode::NOT_FOUND
                );
            }

            $user->delete();
            Log::info('User deleted successfully', ['id' => $id]);
            return $this->successResponse(
                'User deleted successfully',
                null,
                ResponseCode::SUCCESS
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage(), ['id' => $id]);
            return $this->errorResponse(
                'An error occurred while deleting user',
                null,
                ResponseCode::INTERNAL_ERROR
            );
        }
    }
}
