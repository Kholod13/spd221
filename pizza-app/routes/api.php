<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;

Route::get('/categories', [CategoryController::class, 'index']); // Отримати всі категорії
Route::get('/categories/{id}', [CategoryController::class, 'show']); // Отримати одну категорію за ID
Route::post('/categories', [CategoryController::class, 'store']); // Створити нову категорію
Route::put('/categories/{id}', [CategoryController::class, 'update']); // Оновити категорію за ID
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // Видалити категорію за ID
