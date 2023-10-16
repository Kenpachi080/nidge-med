<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{

    public function pay()
    {
        $defaultSettings = [
            'pg_order_id' => '123456',
            'pg_merchant_id' => '544905',
            'pg_amount' => '100',
            'pg_description' => 'Test payment 7',
            'pg_testing_mode' => '1',
            'pg_salt' => 'evrika',
        ];

        $defaultSettings['pg_sig'] = $this->signature($defaultSettings);
        $response = Http::post('https://api.freedompay.money/init_payment.php', $defaultSettings);

        return $response->body();
    }

    public function signature($request): string
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
}
