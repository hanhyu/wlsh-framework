<?php
declare(strict_types=1);
function test(string $value)
{
    return 'function-' . $value . PHP_EOL;
}

function http_response1(int $code = 200, string $msg = '', array $data = []): string
{
    $result         = [];
    $result['code'] = $code;
    $result['data'] = $data;
    try {
        $res = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    } catch (Throwable $e) {
        $result['code'] = 400;
        $result['msg']  = $e->getMessage();
        $result['data'] = [];
        $res            = json_encode($result, JSON_UNESCAPED_UNICODE);
        return $res;
    }
    return $res;
}
