<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-8-1
 * Time: 下午9:03
 */

use App\Library\DI;
use MongoDB\Driver\Manager;
use Monolog\Handler\MongoDBHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\UidProcessor;
use Swoole\Coroutine;

/**
 * http协议以固定json格式返回信息
 *
 * @param int    $code
 * @param string $msg
 * @param array  $data
 * @param bool   $vail
 *
 * @return string
 * @throws JsonException
 */
function http_response(int $code = 200, string $msg = 'success', array $data = [], bool $vail = false): string
{
    $result         = [];
    $result['code'] = $code;

    //由于只是获取header中的language值，为静态值，所以这里无需考虑协程数据混乱问题。
    $cid       = Coroutine::getCid();
    $lang_code = DI::get('request_obj' . $cid)->header['language'] ?? '';

    //屏蔽中文简体
    if ('zh-cn' === $lang_code) {
        $vail = true;
    }

    if ($msg and !$vail and $lang_code) {
        $result['msg'] = LANGUAGE[$lang_code][$msg] ?? '国际化：非法请求参数';
    } else {
        $result['msg'] = $msg;
    }

    $result['data'] = $data;
    try {
        $res = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    } catch (Throwable $e) {
        $result['code'] = 400;
        $result['msg']  = $e->getMessage();
        $result['data'] = [];
        $res            = json_encode($result, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        return $res;
    }
    //debug_print_backtrace();
    return $res;
}

/**
 * ws协议以固定json格式返回信息
 *
 * @param int    $code
 * @param string $uri
 * @param string $msg
 * @param array  $data
 * @param bool   $vail
 *
 * @return string
 */
function ws_response(int $code = 200, string $uri = '', string $msg = '', array $data = [], bool $vail = false): string
{
    $result         = [];
    $result['code'] = $code;
    $result['uri']  = $uri;

    $lang_code = DI::get('ws_language_str');
    if ('zh-cn' === $lang_code) {
        $vail = true;
    }
    if ($msg and !$vail and $lang_code) {
        $result['msg'] = LANGUAGE[$lang_code][$msg] ?? '国际化：非法请求参数';
    } else {
        $result['msg'] = $msg;
    }

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


/**
 * 协程记录日志文件信息
 * 注意：如果是耗时的日志记录，必须使用task_log方法异步处理，
 * 推荐在请求的路由生命周期内都使用task_log记录日志，
 * critica、alert、emergency三种日志类型默认添加邮件通知。
 *
 * @param mixed  $content
 * @param string $info
 * @param string $channel
 * @param string $level <p>debug (100): 详细的debug信息</p></P>
 *                      <p>info (200): 有意义的事件，比如用户登录、SQL日志.</P>
 *                      <p>notice (250): 普通但是重要的事件</P>
 *                      <p>warning (300): 异常事件，但是并不是错误。比如使用了废弃了的API，错误地使用了一个API，以及其他不希望发生但是并非必要的错误.</P>
 *                      <p>error (400): 运行时的错误，不需要立即注意到，但是需要被专门记录并监控到.</P>
 *                      <p>critica (500): 严重错误</P>
 *                      <p>alert (550): 必须立即采取行动。比如整个网站都挂了，数据库不可用了等。这种情况应该发送短信警报，并把你叫醒.</P>
 *                      <p>emergency (600): 紧急请求：系统不可用了</P>
 *
 * @throws Exception
 */
function co_log($content, string $info, string $channel = 'system', string $level = 'info'): void
{
    if ($level === 'critica' or $level === 'alert' or $level === 'emergency') {
        go(static function () use ($content, $info) {
            send_email($content, $info);
        });
    }
    if (APP_DEBUG) {
        go(static function () use ($content, $info, $channel, $level) {
            //使用文件存储日志，mongodb不支持在协程端。
            monolog_by_file($content, $info, $channel, $level);
        });
    }
}

/**
 * monolog使用mongodb驱动记录日志
 * 添加log日志模块,使用ini_get获取yaf配置节，达到动态分类显示日志，如：开发环境日志，测试环境日志，生产环境日志等
 *
 * @param        $content
 * @param string $info
 * @param string $channel
 * @param string $level
 *
 * @return bool
 */
function monolog_by_mongodb($content, string $info, string $channel, string $level): bool
{
    try {
        $log    = new Logger($channel);
        $config = DI::get('config_arr')['log'];

        $log->pushHandler(new MongoDBHandler(
            new Manager($config['mongo'], [
                'username'   => $config['username'],
                'password'   => $config['pwd'],
                'authSource' => $config['database'],
            ]),
            $config['database'],
            $config['collection'],
            $level
        ));

        $log->pushProcessor(new ProcessIdProcessor());
        $log->pushProcessor(new UidProcessor());
        $log->pushProcessor(new MemoryUsageProcessor());
        $log->pushProcessor(new MemoryPeakUsageProcessor());

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
 * @param string $channel
 * @param string $level
 *
 * @throws Exception
 */
function monolog_by_file($content, string $info, string $channel, string $level): void
{
    $dir = date("Y-m-d");
    $log = new Logger($channel);
    $log->pushHandler(new StreamHandler(ROOT_PATH . "/log/monolog/{$dir}.log", Monolog\Logger::DEBUG));
    $log->pushProcessor(new ProcessIdProcessor());
    $log->pushProcessor(new UidProcessor());
    $log->pushProcessor(new MemoryUsageProcessor());
    $log->pushProcessor(new MemoryPeakUsageProcessor());
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
    $email     = DI::get('email_config_arr')[CURRENT_ENV];
    $transport = (new Swift_SmtpTransport($email['host'], $email['port']))
        ->setUsername($email['uname'])
        ->setPassword($email['pwd']);

    $mailer  = new Swift_Mailer($transport);
    $body    = $info . '<br />' . $content . '<br />记录时间：' . date('Y-m-d H:i:s');
    $message = (new Swift_Message($email['subject']))
        ->setFrom($email['from'])
        ->setTo($email['to'])
        ->setBody($body, 'text/html', 'utf-8');

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
 * @param string                   $channel
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
function task_log(Swoole\WebSocket\Server $server, $data, string $info, string $channel = 'system', string $level = 'info'): void
{
    $tasks['uri']     = '/task/log/index';
    $tasks['content'] = $data;
    $tasks['info']    = $info;
    $tasks['channel'] = $channel;
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
    if (!empty($server['http_client_ip'])) {
        $cip = $server['http_client_ip'];
    } elseif (!empty($server['http_x_forwarded_for'])) {
        $cip = $server['http_x_forwarded_for'];
    } elseif (!empty($server['remote_addr'])) {
        $cip = $server['remote_addr'];
    } else {
        $cip = '';
    }
    preg_match("/[\d\.]{7,15}/", $cip, $cips);
    $cip = $cips[0] ?? 'unknown';
    unset($cips);

    return $cip;
}

/**
 * php时间戳带上微秒（13位）
 * @return float
 */
function msectime(): float
{
    [$msec, $sec] = explode(' ', microtime());
    return (float)sprintf('%.0f', ((float)$msec + (float)$sec) * 1000);
}

/**
 * 验证token的合法性、是否存在与过期
 * UserDomain: hanhyu
 * Date: 19-5-21
 * Time: 下午4:24
 *
 * @param string $token
 *
 * @return array
 * @throws JsonException
 */
function validate_token(string $token): array
{
    $res['code'] = 401;

    if (empty($token)) {
        $res['msg'] = '请先登录';
        return $res;
    }

    $data = get_token_params($token);

    if (empty($data)) {
        co_log($token, 'validate_token data fail:');
        $res['msg'] = '非法操作';
    } else {
        //设置登录时长过期时间
        $time_flag = (time() - (int)$data['time']) > (int)DI::get('config_arr')['token']['expTime'];
        if ($time_flag) {
            $res['msg'] = '登录超时';
        } else {
            $res = [];
        }
    }

    return $res;
}

/**
 * token加密参数
 *
 * @param array $params
 *
 * @return string
 * @throws JsonException
 */
function get_token(array $params): string
{
    $encrypted = openssl_encrypt(
        json_encode($params, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        'aes-256-cbc',
        base64_decode(DI::get('config_arr')['token']['encryptKey']),
        OPENSSL_RAW_DATA,
        base64_decode(DI::get('config_arr')['token']['encryptIv'])
    );
    return base64_encode($encrypted);
}

/**
 * token解密参数
 *
 * @param string $auth
 *
 * @return array
 * @throws JsonException
 */
function get_token_params(string $auth): array
{
    $data      = [];
    $token     = base64_decode($auth);
    $decrypted = openssl_decrypt(
        $token,
        'aes-256-cbc',
        base64_decode(DI::get('config_arr')['token']['encryptKey']),
        OPENSSL_RAW_DATA,
        base64_decode(DI::get('config_arr')['token']['encryptIv'])
    );
    if ($decrypted) {
        $res = json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR);
        if (json_last_error() === 0) {
            $data = $res;
        }
    }
    return $data;
}

/**
 * 数据签名规则
 * 如需要对返回的数据进行加密，请自行用https私钥加密，客户端可以用https公钥解密。
 *
 * @param int    $cid
 * @param string $data
 */
function sign(int $cid, string $data): void
{
    $data = stripslashes($data);
    //简单的sign签名，如需用app_id、app_key颁发认证签名时请放进redis中和noncestr随机数
    if (DI::get('config_arr')['sign']['flag']) {
        $time = time();
        /*
        $sign = private_encrypt(
                    md5($data . $time),
                    DI::get('config_arr')['sign']['prv_key']
                );
        */
        $sign = md5($data . $time);

        $resp = DI::get('response_obj' . $cid);
        $resp->header('timestamp', (string)$time);
        $resp->header('sign', $sign);
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
    }

    return false;
}

/**
 * @param $password
 *
 * @return false|int
 */
function is_md5($password)
{
    return preg_match('/^[a-f0-9]{32}$/', $password);
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

/**
 * 数组分页
 * 一维数组直接返回分页键值数据；
 * 二维数组返回第二维数组键值数据
 *
 * User: hanhyu
 * Date: 2020/3/15
 * Time: 下午1:02
 *
 * @param array $array_data 数组
 * @param int   $page       第几页
 * @param int   $page_size  每页显示多少条
 *
 * @return array
 */
function array_to_page_data(array $array_data = [], int $page = 1, int $page_size = 10): array
{
    $array_data                                = array_values($array_data);
    $page_data['list']                         = array_slice($array_data, ($page - 1) * $page_size, $page_size);
    $page_data['pagination']['total']          = count($array_data);
    $page_data['pagination']['current_page']   = count($array_data);
    $page_data['pagination']['pre_page_count'] = $page_size;
    return $page_data;
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
