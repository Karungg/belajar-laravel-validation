<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FormController extends Controller
{
    public function login(Request $request): Response
    {
        try {
            $data = $request->validate([
                "username" => "required",
                "password" => "required"
            ]);
            // do something with $data
            return response("OK", Response::HTTP_OK);
        } catch (ValidationException $exception) {
            return response($exception->errors(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function form(): Response
    {
        return response()->view("form");
    }

    public function submitForm(LoginRequest $request): Response
    {
        $data = $request->validated();
        Log::info(json_encode($request->all(), JSON_PRETTY_PRINT));
        return response("OK", Response::HTTP_OK);
    }
}
