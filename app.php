<?php

require_once 'vendor/autoload.php';

function printResponse(array $response) {
    header('content-type: text/plain; version=0.0.4');

    foreach ($response as $key => $val) {
        echo "$key $val\n";
    }
}

function log1(string $log) {
    $log = sprintf(
        "[%s] %s\n",
        (new \DateTime)->format('c'),
        $log
    );
    file_put_contents('ges.log', $log , FILE_APPEND);
}

$response = [
    'is_active' => -1,
    'is_for_sale' => -1,
    'quantity' => -1,
    'is_for_booking' => -1,
];

$client = new GuzzleHttp\Client();

try {
    $res = $client->request('GET',
                            'https://v-a-c-special.lastick.ru/api/widget/v1/schedule/9c32f00e-f025-41e5-88ce-bbcb2d9c7e24/tickets',
                            [
                                'headers' => [
                                    'authority' => 'v-a-c-special.lastick.ru',
                                    'sec-ch-ua' => ' Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"',
                                    'accept' => 'application/json, text/plain, */*',
                                    'authorization' => ''
                                ]
                            ]
    );
} catch (\GuzzleHttp\Exception\ClientException $e) {
    $log = sprintf(
        "request failed - %s\nheaders: %s;\nresponse data: %s",
        $e->getMessage(),
        json_encode($e->getResponse()->getHeaders()),
        $e->getResponse()->getBody()->getContents()
    );
    log1($log);
    die(500);
}

$body = $res->getBody()->getContents();

$body = json_decode($body, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    log1("json decode error: " . json_last_error_msg());
    die(500);
}

if (!array_key_exists('tickets', $body)) {
    log1("response key 'tickets' missed");
    die(500);
}

$tickets = $body['tickets'];

if (!is_array($tickets) || count($tickets) != 1) {
    log1("invalid value in 'tickets': " . json_encode($tickets));
    die(500);
}

$tickets = $tickets[0];

if (!array_key_exists('quantity', $tickets)) {
    log1("response key 'tickets.quantity' missed");
    die(500);
}

if (!array_key_exists('is_for_sale', $tickets)) {
    log1("response key 'tickets.is_for_sale' missed");
    die(500);
}

if (!array_key_exists('is_active', $tickets)) {
    log1("response key 'tickets.is_active' missed");
    die(500);
}

if (!array_key_exists('is_for_booking', $tickets)) {
    log1("response key 'tickets.is_for_booking' missed");
    die(500);
}

$response['quantity'] = (int)$tickets['quantity'];
$response['is_for_sale'] = (int)$tickets['is_for_sale'];
$response['is_active'] = (int)$tickets['is_active'];
$response['is_for_booking'] = (int)$tickets['is_for_booking'];

printResponse($response);