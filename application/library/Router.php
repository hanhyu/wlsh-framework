<?php declare(strict_types=1);


namespace App\Library;


#[\Attribute] class Router
{
    /**
     * Router constructor.
     *
     * @param string $method          请求的http方法：GET、POST、PUT、DELETE
     * @param bool   $auth            需要使用authorization进行token认证的路由，false不需要，true需要
     * @param int    $rate_limit      代表该接口服务限流参数 使用nginx配置代替
     * @param int    $circuit_breaker 代表该接口服务超时熔断参数  使用nginx配置代替
     * @param string $before          请求方法之前执行,一般是权限检查动作，用户登录日志，重要数据查询日志，数据删除日志，重要数据变更日志 （如密码变更，权限变更，数据修改等）
     * @param string $after           请求方法之后执行
     */
    public function __construct(
        public string $method = 'GET',
        public bool $auth = true,
        public int $rate_limit = 0,
        public int $circuit_breaker = 0,
        public string $before = '',
        public string $after = ''
    )
    {
        $this->method = strtoupper($method);
    }
}
