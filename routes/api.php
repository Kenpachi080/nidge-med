<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('payment/success', [App\Http\Controllers\PaymentController::class, 'success']);
Route::get('payment/fail', [App\Http\Controllers\PaymentController::class, 'fail']);

Route::middleware('auth.basic')->group(function(){
    Route::group(['prefix'=>'api'], function(){
        Route::group(['prefix'=>'integration'], function() {
            Route::post('/create_update_apartment_complex', [App\Http\Controllers\ApartmentController::class, 'createUpdateApartmentComplex']);
            Route::post('/create_update_apartment', [App\Http\Controllers\ApartmentController::class, 'createUpdateApartment']);
            Route::post('/create_price_list', [App\Http\Controllers\IntegrationController::class, 'createApartmentPrices']);
            Route::post('/create_apartment_states', [App\Http\Controllers\IntegrationController::class, 'createApartmentStates']);
            Route::post('/create_user_debts', [App\Http\Controllers\IntegrationController::class, 'createUserDebt']);
        });
    });
});

Route::get('/test', function () {
    $response = Http::post('https://api.freedompay.money/init_payment.php', [
        'pg_order_id' => '123456',
        'pg_merchant_id' => '544905',
        'pg_amount' => '100',
        'pg_description' => 'Test payment 7',
        'pg_sig' => '774736883fd2a48747e8beb08c07f7a8',
        'pg_testing_mode' => '1',
        'pg_salt' => 'evrika',
    ]);

    $data = $response->body(); // Получить JSON-ответ

    dd($data);
// По желанию, вы также можете добавить обработку ошибок.
    if ($response->failed()) {
        // Обработка ошибки
        $errorMessage = $response->body();
        // Ваша логика обработки ошибки
    }
});
