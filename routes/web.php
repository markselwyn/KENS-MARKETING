<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/sales', function () {
    return view('sales');
});

Route::get('/inventory', function () {
    return view('inventory');
});

Route::get('/reports', function () {
    return view('reports');
});

// The Final Route: DSS Insights
Route::get('/dss-insights', function () {
    return view('dss-insights');
});