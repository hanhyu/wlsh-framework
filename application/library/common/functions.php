<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-8-1
 * Time: 下午9:03
 */

/**
 * http协议以固定json格式返回信息
 * php 7.3 版本
 *
 * @param int          $code
 * @param string|array $data
 * @param int          $msg 注意：此参数值默认为0,不返回给客户端，只有在设置了数值时才参与返回数据
 *                          <p>此参数作为非正常数据返回时带上的提示码，便于测试、运维人员在后台直接查看出现异常的原因</P>
 *                          <p>设置的值必须是<b>运维平台“非正常提示码”的相关ID</b></P>
 *
 * @return string
 */
function http_response(int $code = 200, $data = '', int $msg = 0): string
{
    $result         = [];
    $result['code'] = $code;
    $result['data'] = $data;
    if ($msg !== 0) $result['msg'] = $msg;
    try {
        $res = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    } catch (Throwable $e) {
        $result['code'] = 400;
        $result['data'] = $e->getMessage();
        $res            = json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    sign(stripslashes($res));
    //debug_print_backtrace();
    return $res;
}

/**
 * ws协议以固定json格式返回信息
 *
 * @param int         $code
 * @param string|null $uri
 * @param mixed       $data
 *
 * @return string
 */
function ws_response(int $code = 200, ?string $uri = null, $data = ''): string
{
    $result         = [];
    $result['code'] = $code;
    $result['uri']  = $uri;
    $result['data'] = $data;
    try {
        $res = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    } catch (Throwable $e) {
        $result['code'] = 400;
        $result['data'] = $e->getMessage();
        $res            = json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    return $res;
}

/**
 * 协程记录日志信息
 * 注意：如果是耗时的日志记录，必须使用task_log方法异步处理，
 * 推荐在请求的路由生命周期内都使用task_log记录日志，
 * critica、alert、emergency三种日志类型默认添加邮件通知。
 *
 * @param  mixed $content
 * @param string $info
 * @param string $level <p>debug (100): 详细的debug信息</p></P>
 *                      <p>info (200): 有意义的事件，比如用户登录、SQL日志.</P>
 *                      <p>notice (250): 普通但是重要的事件</P>
 *                      <p>warning (300): 异常事件，但是并不是错误。比如使用了废弃了的API，错误地使用了一个API，以及其他不希望发生但是并非必要的错误.</P>
 *                      <p>error (400): 运行时的错误，不需要立即注意到，但是需要被专门记录并监控到.</P>
 *                      <p>critica (500): 严重错误</P>
 *                      <p>alert (550): 必须立即采取行动。比如整个网站都挂了，数据库不可用了等。这种情况应该发送短信警报，并把你叫醒.</P>
 *                      <p>emergency (600): 紧急请求：系统不可用了</P>
 *
 */
function co_log($content, string $info, string $level = 'info'): void
{
    if ($level == 'critica' or $level == 'alert' or $level == 'emergency') {
        go(function () use ($content, $info) {
            send_email($content, $info);
        });
    }
    if (APP_DEBUG) {
        go(function () use ($content, $info, $level) {
            $let = monolog_by_mongodb($content, $info, $level);
            if (!$let) { //如果使用mongodb记录日志失败，则使用文件存储日志。
                monolog_by_file($content, $info, $level);
            }
        });
    }
}

/**
 * monolog使用mongodb驱动记录日志
 * 添加log日志模块,使用ini_get获取yaf配置节，达到动态分类显示日志，如：开发环境日志，测试环境日志，生产环境日志等
 *
 * @param        $content
 * @param string $info
 * @param string $level
 *
 * @return bool
 */
function monolog_by_mongodb($content, string $info, string $level): bool
{
    $log = new \Monolog\Logger(ini_get('yaf.environ'));
    $log->pushHandler(new \Monolog\Handler\MongoDBHandler(
        new \MongoDB\Driver\Manager(Yaf\Registry::get('config')->log->mongo),
        Yaf\Registry::get('config')->log->database,
        Yaf\Registry::get('config')->log->collection,
        $level
    ));

    $log->pushProcessor(new \Monolog\Processor\ProcessIdProcessor());
    $log->pushProcessor(new \Monolog\Processor\UidProcessor());
    $log->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor());
    $log->pushProcessor(new \Monolog\Processor\MemoryPeakUsageProcessor());
    try {
        if (is_array($content)) {
            $log->$level($info, $content);
        } else {
            $log->$level($info, ['data' => $content]);
        }
    } catch (Throwable $e) {
        send_email($e, '记录日志使用mongodb驱动失败：');
        return false;
    }
    return true;
}

/**
 * monolog使用本地文件存储记录日志
 * 添加log日志模块,使用ini_get获取yaf配置节，达到动态分类显示日志，如：开发环境日志，测试环境日志，生产环境日志等
 *
 * @param        $content
 * @param string $info
 * @param string $level
 *
 * @throws Exception
 */
function monolog_by_file($content, string $info, string $level): void
{
    $dir = date("Y-m-d", time());
    $log = new \Monolog\Logger(ini_get('yaf.environ'));
    $log->pushHandler(new \Monolog\Handler\StreamHandler(ROOT_PATH . "/log/monolog/{$dir}.log", Monolog\Logger::DEBUG));
    $log->pushProcessor(new \Monolog\Processor\ProcessIdProcessor());
    $log->pushProcessor(new \Monolog\Processor\UidProcessor());
    $log->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor());
    $log->pushProcessor(new \Monolog\Processor\MemoryPeakUsageProcessor());
    if (is_array($content)) {
        $log->$level($info, $content);
    } else {
        $log->$level("$info {$content}");
    }
}

/**
 * 发送邮件
 *
 * @param        $content
 * @param string $info
 */
function send_email($content, string $info): void
{
    $email     = \Yaf\Registry::get('email_config')->toArray()[ini_get('yaf.environ')];
    $transport = (new Swift_SmtpTransport($email['host'], $email['port']))
        ->setUsername($email['uname'])
        ->setPassword($email['pwd']);

    $mailer  = new Swift_Mailer($transport);
    $body    = $info . $content . '<br />记录时间：' . date('Y-m-d H:i:s');
    $message = (new Swift_Message($email['subject']))
        ->setFrom($email['from'])
        ->setTo($email['to'])
        ->setBody($body, 'text/html');

    $mailer->send($message);
}

/**
 * 异步记录日志
 * 耗时的操作需使用此方法来处理，该方法是异步非阻塞模式。
 * 注意：如果是耗时的日志记录，必须使用task_log方法异步处理，
 * 推荐在请求的路由生命周期内都使用task_log记录日志，
 * critica、alert、emergency三种日志类型默认添加邮件通知。
 *
 * @param \Swoole\WebSocket\Server $server
 * @param                          $data
 * @param string                   $info
 * @param string                   $level <p>debug (100): 详细的debug信息</p></P>
 *                                        <p>info (200): 有意义的事件，比如用户登录、SQL日志.</P>
 *                                        <p>notice (250): 普通但是重要的事件</P>
 *                                        <p>warning (300):
 *                                        异常事件，但是并不是错误。比如使用了废弃了的API，错误地使用了一个API，以及其他不希望发生但是并非必要的错误.</P>
 *                                        <p>error (400): 运行时的错误，不需要立即注意到，但是需要被专门记录并监控到.</P>
 *                                        <p>critica (500): 严重错误</P>
 *                                        <p>alert (550): 必须立即采取行动。比如整个网站都挂了，数据库不可用了等。这种情况应该发送短信警报，并把你叫醒.</P>
 *                                        <p>emergency (600): 紧急请求：系统不可用了</P>
 */
function task_log(Swoole\WebSocket\Server &$server, $data, string $info, string $level): void
{
    $tasks['uri']     = '/task/log/index';
    $tasks['content'] = $data;
    $tasks['info']    = $info;
    $tasks['level']   = $level;
    $send             = serialize($tasks);
    $server->task($send);
}

/**
 * swoole_http_sever获取用户IP地址
 *
 * @param array $server swoole_http_request->$server属性数组
 *
 * @return mixed|string
 */
function get_ip(array $server): string
{
    if (!empty($server["http_client_ip"])) {
        $cip = $server["http_client_ip"];
    } else if (!empty($server["http_x_forwarded_for"])) {
        $cip = $server["http_x_forwarded_for"];
    } else if (!empty($server["remote_addr"])) {
        $cip = $server["remote_addr"];
    } else {
        $cip = '';
    }
    preg_match("/[\d\.]{7,15}/", $cip, $cips);
    $cip = isset($cips[0]) ? $cips[0] : 'unknown';
    unset($cips);

    return $cip;
}

/**
 * 时间戳带上微秒（13位）
 * @return float
 */
function msectime(): float
{
    list($msec, $sec) = explode(' ', microtime());
    $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    return $msectime;
}

/**
 * 验证token的合法性、是否存在与过期
 *
 * @return string
 */
function validate_token(): string
{
    $headers = Yaf\Registry::get('request')->header;
    $token   = $headers['authorization'] ?? 0;

    if (empty($token)) {
        return '请先登录';
    }

    $data = get_token_params((string)$token);

    if (empty($data)) {
        co_log($token, 'validate_token data fail:');
        return '非法操作';
    }

    //设置登录时长过期时间
    if ((time() - (int)$data['time']) > (int)\Yaf\Registry::get('config')->token->expTime) {
        return '登录超时';
    }
    return '0';
}

/**
 * token加密参数
 *
 * @param array $params
 *
 * @return string
 */
function get_token(array $params): string
{
    $encrypted = openssl_encrypt(
        json_encode($params, JSON_UNESCAPED_UNICODE),
        'aes-256-cbc',
        base64_decode(\Yaf\Registry::get('config')->token->encryptKey),
        OPENSSL_RAW_DATA,
        base64_decode(\Yaf\Registry::get('config')->token->encryptIv)
    );
    $encode    = base64_encode($encrypted);

    return $encode;
}

/**
 * token解密参数
 *
 * @param string $auth
 *
 * @return array
 */
function get_token_params(string $auth): array
{
    $data      = [];
    $token     = base64_decode($auth);
    $decrypted = openssl_decrypt(
        $token,
        'aes-256-cbc',
        base64_decode(\Yaf\Registry::get('config')->token->encryptKey),
        OPENSSL_RAW_DATA, base64_decode(\Yaf\Registry::get('config')->token->encryptIv)
    );
    if ($decrypted) {
        $res = json_decode($decrypted, true);
        if (json_last_error() == 0) {
            $data = $res;
        }
    }
    return $data;
}

/**
 * 数据签名规则
 * 如需要对返回的数据进行加密，请自行用https私钥加密，客户端可以用https公钥解密。
 *
 * @param string $data
 */
function sign(string $data): void
{
    //简单的sign签名，如需用app_id、app_key颁发认证签名时请放进redis中和noncestr随机数
    if (Yaf\Registry::get('config')->sign->flag) {
        $time = time();
        /*        $sign = private_encrypt(
                    md5($data . $time),
                    Yaf\Registry::get('config')->sign->prv_key
                );*/
        $sign = md5($data . $time);
        Yaf\Registry::get('response')->header('timestamp', (string)$time);
        Yaf\Registry::get('response')->header('sign', $sign);
    }
}

/**
 * 私钥加密
 *
 * @param string $data
 * @param string $prv_key
 *
 * @return string
 */
function private_encrypt(string $data, string $prv_key): string
{
    $encrypted = '';
    $key       = openssl_pkey_get_private($prv_key);
    openssl_private_encrypt($data, $encrypted, $key);
    return base64_encode($encrypted);
}

/**
 * 公钥加密
 *
 * @param string $data
 * @param string $pub_key
 *
 * @return string
 */
function public_encrypt(string $data, string $pub_key): string
{
    $encrypted = '';
    $key       = openssl_pkey_get_public($pub_key);
    openssl_public_encrypt($data, $encrypted, $key);
    return base64_encode($encrypted);
}

/**
 * 私钥解密
 *
 * @param string $data
 * @param string $prv_key
 *
 * @return string
 */
function private_decrypt(string $data, string $prv_key): string
{
    $decrypted = '';
    $key       = openssl_pkey_get_private($prv_key);
    openssl_private_decrypt(base64_decode($data), $decrypted, $key);
    return $decrypted;
}

/**
 * 公钥解密
 *
 * @param string $data
 * @param string $pub_key
 *
 * @return string
 */
function public_decrypt(string $data, string $pub_key): string
{
    $decrypted = '';
    $key       = openssl_pkey_get_public($pub_key);
    openssl_public_decrypt(base64_decode($data), $decrypted, $key);
    return $decrypted;
}


/**
 * 验证手机号
 *
 * @param string $text
 *
 * @return bool
 */
function is_mobile(string $text): bool
{
    $search = '/^0?1[3|4|5|6|7|8|9][0-9]\d{8}$/';
    if (preg_match($search, $text)) {
        return true;
    } else {
        return false;
    }
}

/**
 * @param $password
 *
 * @return false|int
 */
function is_md5($password)
{
    return preg_match("/^[a-f0-9]{32}$/", $password);
}

/**
 * 把下划线分隔命名的字符串转换成驼峰式命名方式
 *
 * @param string $str     需要转换的字符串
 * @param bool   $ucfirst 转换成大驼峰还是小驼峰，默认true大驼峰，值为false小驼峰
 *
 * @return string
 */
function convert_string(string $str, $ucfirst = true): string
{
    $str = ucwords(str_replace('_', ' ', $str));
    $str = str_replace(' ', '', lcfirst($str));
    return $ucfirst ? ucfirst($str) : $str;
}

/*function update_file(string $url, string $path, int $id)
{
    $data = '';
    $cli = new Swoole\Coroutine\Http\Client($url, 80);
    $cli->setHeaders([
        'Host' => $url,
    ]);
    $cli->set(['timeout' => 30]);
    $cli->addFile($path, 'path', 'text/plain');
    $cli->post('/post', ['id' => $id]);
    if (empty($cli->body)) {
        if ($cli->statusCode == -1) {
            $data = "连接服务器超时";
        } else if ($cli->statusCode == -2) {
            $data = "服务器响应超时";
        }
    } else {
        $data = $cli->body;
    }
    $cli->close();
    return $data;
}*/
