<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    public function pay(PayRequest $request)
    {
        $data  = $request->validated();

        $payment = Payment::create($data);
        $defaultSettings = [
            'pg_order_id' => (string)$payment->id,
            'pg_merchant_id' => '544905',
            'pg_amount' => (string)$payment->amount,
            'pg_description' => 'Test payment 7',
            'pg_testing_mode' => '1',
            'pg_salt' => 'evrika',
            'pg_result_url' => route('pay.callback'),
            'pg_request_method' => 'POST',
            'pg_success_url' => route('pay.callback'),
            'pg_failure_url' => route('pay.callback'),
            'pg_success_url_method' => 'POST',
            'pg_failure_url_method' => 'POST'
        ];


        $defaultSettings['pg_sig'] = $this->signature($defaultSettings);

        $response = Http::withOptions([
            'verify' => false
        ])->post('https://api.freedompay.money/init_payment.php', $defaultSettings);

        $xml = new \SimpleXMLElement($response->body());
        return response([
            'status' => $xml->pg_status,
            'pg_redirect_url' => $xml->pg_redirect_url,
        ]);
    }

    private function signature($request): string
    {
        $requestForSignature = $this->makeFlatParamsArray($request);

        ksort($requestForSignature); // Сортировка по ключу
        array_unshift($requestForSignature, 'init_payment.php'); // Добавление в начало имени скрипта
        array_push($requestForSignature, env("FREEDOM_KEY")); // Добавление в конец секретного ключа

        return md5(implode(';', $requestForSignature)); // Полученная подпись
    }

    private function makeFlatParamsArray($arrParams, $parent_name = ''): array
    {
        $arrFlatParams = [];
        $i = 0;
        foreach ($arrParams as $key => $val) {
            $i++;
            $name = $parent_name . $key . sprintf('%03d', $i);
            if (is_array($val)) {
                $arrFlatParams = array_merge($arrFlatParams, $this->makeFlatParamsArray($val, $name));
                continue;
            }
            $arrFlatParams += array($name => (string)$val);
        }

        return $arrFlatParams;
    }

    public function callback(Request $request): void
    {
        Log::info("request", ['data' => $request]);
    }
}
