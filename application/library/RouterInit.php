<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-11-1
 * Time: 下午5:50
 */

namespace App\Library;

use Exception;
use Swoole\Coroutine;

class RouterInit
{
    /**
     * 格式：
     * @router auth=false&method=get
     *
     * 参数说明：
     * auth 值是需要使用authorization进行token认证的路由，false不需要，true需要
     * method 值是请求的http方法
     * rate-limit 值代表该接口服务限流参数 使用nginx配置代替
     * circuit-breaker 值代表该接口服务超时熔断参数  使用nginx配置代替
     * before 请求方法之前执行,一般是权限检查动作，用户登录日志，重要数据查询日志，数据删除日志，重要数据变更日志 （如密码变更，权限变更，数据修改等）
     * after 请求方法之后执行
     *
     * 可以按不同项目、团队、需求、个人喜好等对uri增加加密key
     */

    /**
     * User: hanhyu
     * Date: 2019/12/4
     * Time: 下午10:05
     *
     * @param array  $uri_arr 请求的链接地址
     * @param string $method  请求的方法
     *
     * @return string|null
     * @throws ProgramException
     */
    public function routerStartup(array $uri_arr, string $method): ?string
    {
        //todo 使用atomic做接口限流

        /*可以在这个钩子函数routerShutdown中做拦截处理，获取当前URI，以当前URI做KEY，判断是否存在该KEY的缓存，
         若存在则停止解析，直接输出页面，缓存数据页。
         或做防重复操作提交*/

        /**
         * $arr[1] module
         * $arr[2] controller
         * $arr[3] action
         */
        switch (count($uri_arr)) {
            case 5:
                $ctrl   = 'App\Modules\\' . ucfirst($uri_arr[1]) . '\Controllers\\' . ucfirst($uri_arr[2]) . '\\' . ucfirst($uri_arr[3]) . 'Controller';
                $action = $uri_arr[4] . 'Action';
                break;
            case 4:
                $ctrl   = 'App\Modules\\' . ucfirst($uri_arr[1]) . '\Controllers\\' . ucfirst($uri_arr[2]) . 'Controller';
                $action = $uri_arr[3] . 'Action';
                break;
            default:
                $ctrl   = 'App\Controllers\\' . ucfirst($uri_arr[1]) . 'Controller';
                $action = $uri_arr[2] . 'Action';
        }

        try {
            //$ref            = new \ReflectionClass($ctrl);
            // $ref_method_doc = $ref->getMethod($action)->getDocComment();
            // $flag           = preg_match_all('/@router(.*?)\n/', $ref_method_doc, $ref_method_doc);

            $ref     = new \ReflectionMethod($ctrl, $action);
            $ref_arg = $ref->getAttributes()[0]?->getArguments();

            if (empty($ref_arg)) {
                throw new ProgramException('请求的接口不存在', 500);
            }

            $ref_arg['method']          = strtoupper($ref_arg['method']) ?? 'GET';
            $ref_arg['auth']            = $ref_arg['auth'] ?? true;
            $ref_arg['rate-limit']      = $ref_arg['rate-limit'] ?? 0;
            $ref_arg['circuit-breaker'] = $ref_arg['circuit-breaker'] ?? 0;
            $ref_arg['before']          = $ref_arg['before'] ?? '';
            $ref_arg['after']           = $ref_arg['after'] ?? '';

            if ('CLI' === ucfirst($ref_arg['method'])) {
                $ref_arg['method'] = 'Cli';
            }

            if ($method !== $ref_arg['method']) {
                throw new ProgramException('请求方法不正确', 405);
            }

            if (true === $ref_arg['auth']) {
                $this->authToken();
            }

            if (class_exists($ctrl)) {
                $class = new $ctrl();
                if (method_exists($class, $action)) {
                    if (!empty($ref_arg['before'])) {
                        $before_action = $ref_arg['before'];
                        if ($before_res = $class->$before_action()) {
                            return $before_res;
                        }
                    }

                    $res = $class->$action();

                    if (!empty($ref_arg['after'])) {
                        $after_action = $ref_arg['after'];
                        $class->$after_action();
                    }

                    return $res;
                }
            }

            return '非法请求';
        } catch (\ReflectionException) {
            throw new ProgramException('请求的接口不存在', 400);
        }
    }

    /**
     * 请求该路由是否需要授权的token
     *
     * @throws Exception
     * @todo 还需要进一步验证token是否属于用户
     */
    private function authToken(): void
    {
        //验证授权token的合法性与过期时间
        $cid     = Coroutine::getCid();
        $headers = DI::get('request_obj' . $cid)->header;
        $token   = $headers['authorization'] ?? '0';

        $res = validate_token($token);
        if (!empty($res)) {
            throw new ProgramException($res['msg'], $res['code']);
        }
    }
}
