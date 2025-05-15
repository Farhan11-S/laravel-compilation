<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JemaahController;
use App\Http\Controllers\MustahikController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::post('/ocr', function (Request $request) {
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $path = $image->store('images', 'public');

        $output = Http::withHeaders([
            'ngrok-skip-browser-warning' => 'foo',
        ])
            ->attach(
                'ktp_img',
                file_get_contents(storage_path('app/public/' . $path)),
                $image->getClientOriginalName(),
                [
                    'Content-Type' => 'image/jpg',
                ]
            )
            ->post(env('OCR_API_URL', 'http://127.0.0.1:5000'));

        return response()->json([
            'message' => 'Image uploaded successfully',
            'data' => [
                'output' => json_decode($output),
                'path' => $path,
            ]
        ]);
    } else {
        return response()->json(['message' => 'No image found'], 400);
    }
})->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/jemaah', JemaahController::class);
    Route::apiResource('/mustahik', MustahikController::class);
    Route::apiResource('/users', UserController::class);
});
