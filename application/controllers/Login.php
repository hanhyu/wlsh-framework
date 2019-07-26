<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Domain\System\User;
use App\Models\RedisFactory;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Yaf\{
    Controller_Abstract,
    Registry,
};
use Swoole\Coroutine;
use Swoole\WebSocket\Server;
use Swoole\Http\Response;
use Exception;
use App\Domain\Index\Login as LoginDomain;

/**
 * 测试用例
 * User: hanhyu
 * Date: 18-7-25
 * Time: 上午10:24
 */
class Login extends Controller_Abstract
{
    /**
     * @var Server
     */
    private $server;
    /**
     * @var Response
     */
    private $response;
    /**
     * @var \Redis
     */
    private $redis;
    private $cid;

    public function init()
    {
        $this->cid      = Coroutine::getCid();
        $this->server   = Registry::get('server');
        $this->response = Registry::get('response_' . $this->cid);
    }

    /**
     * 此方法不能删除
     * User: hanhyu
     * Date: 19-7-12
     * Time: 上午10:09
     */
    public function indexAction(): void
    {

    }

    /**
     * User: hanhyu
     * Date: 18-7-21
     * Time: 下午3:27
     * ab -c 1000 -n 1000000 -k http://127.0.0.1:9770/login/test
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 100000 requests
     * Completed 200000 requests
     * Completed 300000 requests
     * Completed 400000 requests
     * Completed 500000 requests
     * Completed 600000 requests
     * Completed 700000 requests
     * Completed 800000 requests
     * Completed 900000 requests
     * Completed 1000000 requests
     * Finished 1000000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     *
     * Document Path:          /login/test
     * Document Length:        11 bytes
     *
     * Concurrency Level:      1000
     * Time taken for tests:   12.922 seconds
     * Complete requests:      1000000
     * Failed requests:        0
     * Keep-Alive requests:    1000000
     * Total transferred:      367000000 bytes
     * HTML transferred:       11000000 bytes
     * Requests per second:    77385.08 [#/sec] (mean)
     * Time per request:       12.922 [ms] (mean)
     * Time per request:       0.013 [ms] (mean, across all concurrent requests)
     * Transfer rate:          27734.69 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:        0    0  12.8      0    1048
     * Processing:     3   13   1.3     13      65
     * Waiting:        3   13   1.3     13      65
     * Total:          3   13  13.0     13    1074
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%     13
     * 66%     13
     * 75%     13
     * 80%     13
     * 90%     13
     * 95%     14
     * 98%     14
     * 99%     14
     * 100%   1074 (longest request)
     *
     * ab -c 10000 -n 1000000  http://127.0.0.1:9770/login/test
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 100000 requests
     * Completed 200000 requests
     * Completed 300000 requests
     * Completed 400000 requests
     * Completed 500000 requests
     * Completed 600000 requests
     * Completed 700000 requests
     * Completed 800000 requests
     * Completed 900000 requests
     * Completed 1000000 requests
     * Finished 1000000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     *
     * Document Path:          /login/test
     * Document Length:        11 bytes
     *
     * Concurrency Level:      10000
     * Time taken for tests:   58.512 seconds
     * Complete requests:      1000000
     * Failed requests:        0
     * Total transferred:      362000000 bytes
     * HTML transferred:       11000000 bytes
     * Requests per second:    17090.51 [#/sec] (mean)
     * Time per request:       585.120 [ms] (mean)
     * Time per request:       0.059 [ms] (mean, across all concurrent requests)
     * Transfer rate:          6041.76 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:       23  292 273.5    279    7403
     * Processing:    26  291  51.0    296     407
     * Waiting:        6  213  49.4    218     356
     * Total:         68  583 277.7    582    7708
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%    582
     * 66%    586
     * 75%    589
     * 80%    590
     * 90%    596
     * 95%    601
     * 98%    609
     * 99%    622
     * 100%   7708 (longest request)
     *
     *
     * ab -c 10000 -n 300000 -k  http://127.0.0.1:9770/login/test
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 30000 requests
     * Completed 60000 requests
     * Completed 90000 requests
     * Completed 120000 requests
     * Completed 150000 requests
     * Completed 180000 requests
     * Completed 210000 requests
     * Completed 240000 requests
     * Completed 270000 requests
     * Completed 300000 requests
     * Finished 300000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     *
     * Document Path:          /login/test
     * Document Length:        11 bytes
     *
     * Concurrency Level:      10000
     * Time taken for tests:   4.825 seconds
     * Complete requests:      300000
     * Failed requests:        0
     * Keep-Alive requests:    300000
     * Total transferred:      110100000 bytes
     * HTML transferred:       3300000 bytes
     * Requests per second:    62178.86 [#/sec] (mean)
     * Time per request:       160.826 [ms] (mean)
     * Time per request:       0.016 [ms] (mean, across all concurrent requests)
     * Transfer rate:          22284.81 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:        0   31 247.2      0    3235
     * Processing:    43  110  29.5    117     277
     * Waiting:       43  110  29.5    117     277
     * Total:         43  141 252.1    117    3498
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%    117
     * 66%    131
     * 75%    134
     * 80%    135
     * 90%    137
     * 95%    138
     * 98%    248
     * 99%   1296
     * 100%   3498 (longest request)
     *
     * ab -c 1000 -n 1000000 -k https://127.0.0.1:9770/login/test
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 100000 requests
     * Completed 200000 requests
     * Completed 300000 requests
     * Completed 400000 requests
     * Completed 500000 requests
     * Completed 600000 requests
     * Completed 700000 requests
     * Completed 800000 requests
     * Completed 900000 requests
     * Completed 1000000 requests
     * Finished 1000000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     * SSL/TLS Protocol:       TLSv1.2,ECDHE-RSA-AES256-GCM-SHA384,2048,256
     *
     * Document Path:          /login/test
     * Document Length:        11 bytes
     *
     * Concurrency Level:      1000
     * Time taken for tests:   22.613 seconds
     * Complete requests:      1000000
     * Failed requests:        0
     * Keep-Alive requests:    1000000
     * Total transferred:      475000000 bytes
     * HTML transferred:       11000000 bytes
     * Requests per second:    44222.71 [#/sec] (mean)
     * Time per request:       22.613 [ms] (mean)
     * Time per request:       0.023 [ms] (mean, across all concurrent requests)
     * Transfer rate:          20513.47 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:        0    2  72.4      0    3112
     * Processing:     7   20  24.6     19    1267
     * Waiting:        7   20  24.6     19    1267
     * Total:          7   23  90.3     19    3131
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%     19
     * 66%     20
     * 75%     20
     * 80%     20
     * 90%     20
     * 95%     20
     * 98%     21
     * 99%     22
     * 100%   3131 (longest request)
     *
     * h2load -c 100 -n 300000 -m 100 -t 2  https://127.0.0.1:9770/login/test
     * starting benchmark...
     * spawning thread #0: 50 total client(s). 150000 total requests
     * spawning thread #1: 50 total client(s). 150000 total requests
     * TLS Protocol: TLSv1.2
     * Cipher: ECDHE-RSA-AES256-GCM-SHA384
     * Server Temp Key: ECDH P-384 384 bits
     * Application protocol: h2
     * progress: 10% done
     * progress: 20% done
     * progress: 30% done
     * progress: 40% done
     * progress: 50% done
     * progress: 60% done
     * progress: 70% done
     * progress: 80% done
     * progress: 90% done
     * progress: 100% done
     *
     * finished in 2.91s, 102916.31 req/s, 4.33MB/s
     * requests: 300000 total, 300000 started, 300000 done, 300000 succeeded, 0 failed, 0 errored, 0 timeout
     * status codes: 300000 2xx, 0 3xx, 0 4xx, 0 5xx
     * traffic: 12.62MB (13234277) total, 4.32MB (4531577) headers (space savings 96.14%), 3.15MB (3300000) data
     * min         max         mean         sd        +/- sd
     * time for request:     3.28ms    224.64ms     85.28ms     24.85ms    75.46%
     * time for connect:   108.24ms    343.21ms    242.23ms     41.84ms    68.00%
     * time to 1st byte:   232.91ms    476.04ms    328.62ms     66.44ms    65.00%
     * req/s           :    1029.59     1144.43     1061.31       26.13    68.00%
     *
     * ./wrk -t4 -c1000 -d10s https://127.0.0.1:9770/login/test
     * Running 10s test @ https://127.0.0.1:9770/login/test
     * 4 threads and 1000 connections
     * Thread Stats   Avg      Stdev     Max   +/- Stdev
     * Latency    25.53ms   80.85ms   1.11s    97.31%
     * Req/Sec    15.81k     3.06k   26.88k    79.57%
     * 529140 requests in 10.08s, 239.70MB read
     * Requests/sec:  52473.98
     * Transfer/sec:     23.77MB
     */
    public function testAction(): void
    {
        $this->response->end('hello world');
    }

    public function leveldbAction(): void
    {
        $db = new LevelDB(ROOT_PATH . '/log/level.db');
        $db->put("key", "value");
        $this->response->end($db->get("key"));
    }

    /**
     * ab -c 10000 -n 1000000 -k http://127.0.0.1:9770/login/getRedis/
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 100000 requests
     * Completed 200000 requests
     * Completed 300000 requests
     * Completed 400000 requests
     * Completed 500000 requests
     * Completed 600000 requests
     * Completed 700000 requests
     * Completed 800000 requests
     * Completed 900000 requests
     * Completed 1000000 requests
     * Finished 1000000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     *
     * Document Path:          /login/getRedis/
     * Document Length:        34 bytes
     *
     * Concurrency Level:      10000
     * Time taken for tests:   14.871 seconds
     * Complete requests:      1000000
     * Failed requests:        0
     * Keep-Alive requests:    1000000
     * Total transferred:      379000000 bytes
     * HTML transferred:       34000000 bytes
     * Requests per second:    67245.41 [#/sec] (mean)
     * Time per request:       148.709 [ms] (mean)
     * Time per request:       0.015 [ms] (mean, across all concurrent requests)
     * Transfer rate:          24888.68 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:        0   21 299.2      0    7495
     * Processing:    34  123  29.9    135     296
     * Waiting:       34  123  29.9    135     296
     * Total:         34  144 301.5    138    7788
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%    138
     * 66%    142
     * 75%    142
     * 80%    143
     * 90%    144
     * 95%    146
     * 98%    148
     * 99%    172
     * 100%   7788 (longest request)
     */
    public function getRedisAction(): void
    {
        //$this->redis = \Yaf\Registry::get('redis_pool')->get();
        //$this->redis->select(1);
        //$this->response->end($this->redis->get('key'));
        //\Yaf\Registry::get('redis_pool')->put($this->redis);


        /*
         * 不推荐在协程框架中使用协程隔离的单例模式
         * $value = RedisFactory::login()->getKey('key');
         * */

        //print_r('123');
        //这里会自动触发协程切换
        $value = (new LoginDomain())->getKey('key');
        //print_r('456' . PHP_EOL);
        $resp_content = http_response(200, '', ['content' => $value]);
        sign($this->cid, $resp_content);
        $this->response->end($resp_content);
    }

    public function publisherRedisAction(): void
    {
        $this->redis = \Yaf\Registry::get('redis_pool')->get();
        $let         = $this->redis->xAdd('channel1', '*', ['msg1' => 'hello ceshi']);
        $this->response->end($let);
    }

    public function consumerRedisAction(): void
    {
        $this->redis = \Yaf\Registry::get('redis_pool')->get();
        var_dump($this->redis->xRange('channel1', '-', '+'));
    }

    public function ackRedisAction(): void
    {
        $this->redis = \Yaf\Registry::get('redis_pool')->get();
        //$res = $this->redis->xRange('channel1', '-', '+');
        $this->redis->xGroup('CREATE', 'channel1', 'chgroup4', '0');
        $res = $this->redis->xReadGroup('chgroup4', 'comsumer1', ['channel1' => 0], 2);
        //$let = array_keys($res);
        var_dump(array_keys($res['channel1']));
        $this->redis->xAck('channel1', 'chgroup4', array_keys($res['channel1']));
    }

    public function delRedisAction(): void
    {
        $this->redis = \Yaf\Registry::get('redis_pool')->get();
        $res         = $this->redis->xRange('channel1', '-', '+');
        $this->redis->xDel('channel1', array_keys($res));
    }

    /**
     * elasticsearch
     */
    public function setEsAction(): void
    {
        $hosts  = [
            [
                'host'   => '172.17.0.1',
                'port'   => '9200',
                'scheme' => 'http',
                'user'   => 'elastic',
                'pass'   => 'changme',
            ],
        ];
        $client = Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();
        //$client = \Elasticsearch\ClientBuilder::create()->build();
        $params   = [
            'index' => 'my_index',
            'type'  => 'my_type',
            'id'    => 'my_id',
            'body'  => ['testField' => 'abc', 'ceField' => 'ceshi'],
        ];
        $response = $client->index($params);
        print_r($response);
    }

    /**
     * elasticsearch
     */
    public function getEsAction(): void
    {
        $hosts    = [
            [
                'host'   => '172.17.0.1',
                'port'   => '9200',
                'scheme' => 'http',
                'user'   => 'elastic',
                'pass'   => 'changme',
            ],
        ];
        $client   = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();
        $params   = [
            'index' => 'my_index',
            'type'  => 'my_type',
            'id'    => 'my_id',
        ];
        $response = $client->get($params);
        print_r($response);
    }

    /**
     * xunsearch
     */
    public function setXsAction(): void
    {
        $xs    = new XS('base');
        $index = $xs->getIndex();
        $data  = [
            'pid'     => 234, // 此字段为主键，必须指定
            'subject' => '测试文档的标题',
            'message' => '测试文档的内容部分',
            'chrono'  => time(),
        ];
        $doc   = new XSDocument();
        $doc->setFields($data);
        $res = $index->add($doc);
        print_r($res);
    }

    /**
     * xunsearch
     * @throws XSException
     */
    public function getXsAction(): void
    {
        $xs     = new XS('base');
        $search = $xs->getSearch();
        // $query = '文档'; // 这里的搜索语句很简单，就一个短语
        //$query = '文档 AND 标题'; // 这里的搜索语句很简单，就一个短语
        $query = '无题 AND 测试'; // 这里的搜索语句很简单，就一个短语

        $search->setQuery($query); // 设置搜索语句
        $search->addWeight('subject', 'xunsearch'); // 增加附加条件：提升标题中包含 'xunsearch' 的记录的权重
        $search->setLimit(5); // 设置返回结果最多为 5 条，并跳过前 10 条

        $docs  = $search->search(); // 执行搜索，将搜索结果文档保存在 $docs 数组中
        $count = $search->count(); // 获取搜索结果的匹配总数估算值
        print_r($docs);
        print_r($count);
    }

    /**
     *
     * ab -c 1000 -n 300000 -k http://127.0.0.1:9770/login/get_user_list
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 30000 requests
     * Completed 60000 requests
     * Completed 90000 requests
     * Completed 120000 requests
     * Completed 150000 requests
     * Completed 180000 requests
     * Completed 210000 requests
     * Completed 240000 requests
     * Completed 270000 requests
     * Completed 300000 requests
     * Finished 300000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     *
     * Document Path:          /login/get_user_list
     * Document Length:        1197 bytes
     *
     * Concurrency Level:      1000
     * Time taken for tests:   29.302 seconds
     * Complete requests:      300000
     * Failed requests:        0
     * Keep-Alive requests:    300000
     * Total transferred:      484500000 bytes
     * HTML transferred:       359100000 bytes
     * Requests per second:    10238.13 [#/sec] (mean)
     * Time per request:       97.674 [ms] (mean)
     * Time per request:       0.098 [ms] (mean, across all concurrent requests)
     * Transfer rate:          16147.04 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:        0    1  23.0      0    1025
     * Processing:     6   97   9.1     96     149
     * Waiting:        6   97   9.1     96     149
     * Total:          6   97  24.4     96    1148
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%     96
     * 66%    100
     * 75%    102
     * 80%    104
     * 90%    108
     * 95%    113
     * 98%    118
     * 99%    121
     * 100%   1148 (longest request)
     */
    public function getUserListAction(): void
    {
        $data['curr_page'] = 1;
        $data['page_size'] = 7;
        //print_r('123');
        $res = (new User())->getInfoList($data);
        //print_r('456' . PHP_EOL);
        if ($res) {
            $this->response->end(http_response(200, '', $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

    public function getUserInfoAction(): void
    {
        $user = new \App\Domain\System\User();
        $res  = $user->getInfoByName('ceshi123');
        if ($res) {
            $this->response->end(http_response(200, '', $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

    /**
     * 测试mongodb查询
     * ab -c 7000 -n 300000 -k http://127.0.0.1:9770/login/get_mongo_log_list
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 30000 requests
     * Completed 60000 requests
     * Completed 90000 requests
     * Completed 120000 requests
     * Completed 150000 requests
     * Completed 180000 requests
     * Completed 210000 requests
     * Completed 240000 requests
     * Completed 270000 requests
     * Completed 300000 requests
     * Finished 300000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     *
     * Document Path:          /login/getMongoLogList/
     * Document Length:        34 bytes
     *
     * Concurrency Level:      7000
     * Time taken for tests:   5.008 seconds
     * Complete requests:      300000
     * Failed requests:        0
     * Keep-Alive requests:    300000
     * Total transferred:      113700000 bytes
     * HTML transferred:       10200000 bytes
     * Requests per second:    59908.53 [#/sec] (mean)
     * Time per request:       116.845 [ms] (mean)
     * Time per request:       0.017 [ms] (mean, across all concurrent requests)
     * Transfer rate:          22173.18 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:        0    5  30.9      0     296
     * Processing:    20  108 125.6    103    3063
     * Waiting:       20  108 125.6    103    3063
     * Total:         20  113 147.1    103    3263
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%    103
     * 66%    105
     * 75%    105
     * 80%    106
     * 90%    109
     * 95%    123
     * 98%    222
     * 99%   1128
     * 100%   3263 (longest request)
     */
    public function getMongoLogListAction(): void
    {
        $data['curr_page'] = 1;
        $data['page_size'] = 7;
        $user              = new \App\Services\System\Log();
        $res               = $user->getMongoList($data);
        if ($res) {
            $this->response->end(http_response(200, '', $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

    /**
     * 测试登录日志带条件按数据表直接查询数据
     * ab -c 500 -n 300000  http://127.0.0.1:9770/login/getLogUserList
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 30000 requests
     * Completed 60000 requests
     * Completed 90000 requests
     * Completed 120000 requests
     * Completed 150000 requests
     * Completed 180000 requests
     * Completed 210000 requests
     * Completed 240000 requests
     * Completed 270000 requests
     * Completed 300000 requests
     * Finished 300000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     *
     * Document Path:          /login/getLogUserList
     * Document Length:        1394 bytes
     *
     * Concurrency Level:      500
     * Time taken for tests:   79.580 seconds
     * Complete requests:      300000
     * Failed requests:        0
     * Total transferred:      524100000 bytes
     * HTML transferred:       418200000 bytes
     * Requests per second:    3769.77 [#/sec] (mean)
     * Time per request:       132.634 [ms] (mean)
     * Time per request:       0.265 [ms] (mean, across all concurrent requests)
     * Transfer rate:          6431.43 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:        0    1   4.9      0      69
     * Processing:    11  131  23.8    126     439
     * Waiting:        9  131  23.7    126     426
     * Total:         29  132  23.0    127     439
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%    127
     * 66%    133
     * 75%    138
     * 80%    141
     * 90%    154
     * 95%    180
     * 98%    211
     * 99%    226
     * 100%    439 (longest request)
     */
    public function getLogUserListAction(): void
    {
        $data['curr_page']  = 1;
        $data['page_size']  = 10;
        $data['login_time'] = '2019-01-14';
        $data['uname']      = 'ceshi001';
        $user               = new \App\Domain\System\User();
        $res                = $user->getLogList($data);
        if ($res) {
            $this->response->end(http_response(200, '', $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }

    /**
     * 测试登录日志带条件按视图直接查询数据
     *  ab -c 100 -n 10000 -k http://127.0.0.1:9770/login/get_log_user_view
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 1000 requests
     * Completed 2000 requests
     * Completed 3000 requests
     * Completed 4000 requests
     * Completed 5000 requests
     * Completed 6000 requests
     * Completed 7000 requests
     * Completed 8000 requests
     * Completed 9000 requests
     * Completed 10000 requests
     * Finished 10000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     *
     * Document Path:          /login/getLogUserView
     * Document Length:        1254 bytes
     *
     * Concurrency Level:      100
     * Time taken for tests:   2.134 seconds
     * Complete requests:      10000
     * Failed requests:        0
     * Keep-Alive requests:    10000
     * Total transferred:      16120000 bytes
     * HTML transferred:       12540000 bytes
     * Requests per second:    4686.90 [#/sec] (mean)
     * Time per request:       21.336 [ms] (mean)
     * Time per request:       0.213 [ms] (mean, across all concurrent requests)
     * Transfer rate:          7378.21 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:        0    0   1.5      0      17
     * Processing:     3   21   5.4     20      68
     * Waiting:        3   21   5.4     20      68
     * Total:          3   21   6.0     20      81
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%     20
     * 66%     22
     * 75%     24
     * 80%     24
     * 90%     27
     * 95%     30
     * 98%     37
     * 99%     46
     * 100%     81 (longest request)
     */
    public function getLogUserViewAction(): void
    {
        $data['curr_page']  = 1;
        $data['page_size']  = 10;
        $data['login_time'] = '2019-01-14';
        $data['uname']      = 'ceshi001';
        $user               = new \App\Services\System\User();
        $res                = $user->getLogViewList($data);
        if ($res) {
            $this->response->end(http_response(200, '', $res));
        } else {
            $this->response->end(http_response(500, '查询失败'));
        }
    }


    /**
     * User: hanhyu
     * Date: 19-1-21
     * Time: 下午5:31
     *
     * ab -c 1000 -n 1000000 -k http://127.0.0.1:9770/login/sw_mysql
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 100000 requests
     * Completed 200000 requests
     * Completed 300000 requests
     * Completed 400000 requests
     * Completed 500000 requests
     * Completed 600000 requests
     * Completed 700000 requests
     * Completed 800000 requests
     * Completed 900000 requests
     * Completed 1000000 requests
     * Finished 1000000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     *
     * Document Path:          /login/coMysql
     * Document Length:        48 bytes
     *
     * Concurrency Level:      1000
     * Time taken for tests:   50.841 seconds
     * Complete requests:      1000000
     * Failed requests:        0
     * Keep-Alive requests:    1000000
     * Total transferred:      404000000 bytes
     * HTML transferred:       48000000 bytes
     * Requests per second:    19669.21 [#/sec] (mean)
     * Time per request:       50.841 [ms] (mean)
     * Time per request:       0.051 [ms] (mean, across all concurrent requests)
     * Transfer rate:          7760.12 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:        0    0   0.9      0      48
     * Processing:    11   51  14.3     49     333
     * Waiting:       11   51  14.3     49     333
     * Total:         11   51  14.3     49     333
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%     49
     * 66%     52
     * 75%     54
     * 80%     55
     * 90%     58
     * 95%     64
     * 98%     76
     * 99%     96
     * 100%    333 (longest request)
     *
     */
    public function swMysqlAction(): void
    {
        $sql = "SELECT * FROM `users` WHERE id=1 LIMIT 1 ";

        /*        $sql = "SELECT `id`,`user_name`,`login_dt`,`logout_dt`,`login_ip`
        FROM `user_log_view`
        WHERE ((`login_dt` BETWEEN '2019-01-14' AND '2019-01-15') AND `user_name` = 'ceshi001')
        ORDER BY `id` DESC
        LIMIT 10 OFFSET 0";*/

        /* $sql = "SELECT `id`,`user_name`,`login_dt`,`logout_dt`,`login_ip`
 FROM `user_log_view`
 WHERE (`user_name` = 'ceshi001')
 ORDER BY `id` DESC
 LIMIT 10 OFFSET 0";*/
        $mysql = Yaf\Registry::get('mysql_pool')->get();
        $get   = $mysql->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $this->response->end(http_response(200, '', $get));
    }

    public function swPgsqlAction(): void
    {
        $sql = "SELECT * FROM users WHERE id=1 LIMIT 1 ";
        //$sql = "select * from users";

        $pgsql = Yaf\Registry::get('pgsql_pool')->get();

        $get = $pgsql->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $this->response->end(http_response(200, '', $get));

    }

    /**
     *ab -c 1000 -n 300000 -k http://127.0.0.1:9770/login/co_mysql
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 30000 requests
     * Completed 60000 requests
     * Completed 90000 requests
     * Completed 120000 requests
     * Completed 150000 requests
     * Completed 180000 requests
     * Completed 210000 requests
     * Completed 240000 requests
     * Completed 270000 requests
     * Completed 300000 requests
     * Finished 300000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     *
     * Document Path:          /login/co_mysql
     * Document Length:        90 bytes
     *
     * Concurrency Level:      1000
     * Time taken for tests:   11.136 seconds
     * Complete requests:      300000
     * Failed requests:        63843
     * (Connect: 0, Receive: 0, Length: 63843, Exceptions: 0)
     * Keep-Alive requests:    300000
     * Total transferred:      147841734 bytes
     * HTML transferred:       23041734 bytes
     * Requests per second:    26940.07 [#/sec] (mean)
     * Time per request:       37.119 [ms] (mean)
     * Time per request:       0.037 [ms] (mean, across all concurrent requests)
     * Transfer rate:          12965.06 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:        0    1  38.2      0    1046
     * Processing:     0   36  33.8     31    1110
     * Waiting:        0   36  33.8     31    1110
     * Total:          0   37  51.7     31    1140
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%     31
     * 66%     34
     * 75%     41
     * 80%     49
     * 90%     62
     * 95%     69
     * 98%     78
     * 99%     87
     * 100%   1140 (longest request)
     *
     *
     *
     * 开启预处理
     * ab -c 1000 -n 200000 -k http://127.0.0.1:9770/login/co_mysql
     * This is ApacheBench, Version 2.3 <$Revision: 1807734 $>
     * Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
     * Licensed to The Apache Software Foundation, http://www.apache.org/
     *
     * Benchmarking 127.0.0.1 (be patient)
     * Completed 20000 requests
     * Completed 40000 requests
     * Completed 60000 requests
     * Completed 80000 requests
     * Completed 100000 requests
     * Completed 120000 requests
     * Completed 140000 requests
     * Completed 160000 requests
     * Completed 180000 requests
     * Completed 200000 requests
     * Finished 200000 requests
     *
     *
     * Server Software:        swoole-http-server
     * Server Hostname:        127.0.0.1
     * Server Port:            9770
     *
     * Document Path:          /login/co_mysql
     * Document Length:        88 bytes
     *
     * Concurrency Level:      1000
     * Time taken for tests:   10.346 seconds
     * Complete requests:      200000
     * Failed requests:        74810
     * (Connect: 0, Receive: 0, Length: 74810, Exceptions: 0)
     * Keep-Alive requests:    200000
     * Total transferred:      96311400 bytes
     * HTML transferred:       13111400 bytes
     * Requests per second:    19331.03 [#/sec] (mean)
     * Time per request:       51.730 [ms] (mean)
     * Time per request:       0.052 [ms] (mean, across all concurrent requests)
     * Transfer rate:          9090.81 [Kbytes/sec] received
     *
     * Connection Times (ms)
     * min  mean[+/-sd] median   max
     * Connect:        0    0   1.7      0      28
     * Processing:     1   48 110.6     33    1091
     * Waiting:        1   48 110.6     33    1091
     * Total:          1   48 110.6     33    1091
     *
     * Percentage of the requests served within a certain time (ms)
     * 50%     33
     * 66%     42
     * 75%     47
     * 80%     50
     * 90%     60
     * 95%     75
     * 98%     96
     * 99%   1024
     * 100%   1091 (longest request)
     */
    public function coMysqlAction(): void
    {
        $sql = "select * from `users` where id=? limit 1 ";

        $mysql = Registry::get('co_mysql_pool')->get();
        $stmt  = $mysql->prepare($sql);
        $get   = $stmt->execute([1]);
        $this->response->header('sign', 'qwe123');

        $this->response->end(http_response(200, '', $get));

    }

    public function getCoRedisAction(): void
    {
        //$ch = new Channel(1);
        print_r('123');
        $response = $this->response;
        //go(function () use ($ch, $response) {
        //\Co::sleep(0.1);
        $value = RedisFactory::login()->getKey('key');
        print_r('456' . PHP_EOL);
        //$ch->push($value);
        $response->end($value);
        //});
        //$res = $ch->pop(3);


        //$this->response->end($res);
    }

    /**
     * @throws Exception
     */
    public function setRedisAction(): void
    {
        //$this->redis = \Yaf\Registry::get('redis_pool')->get();
        //$this->redis->select(1);
        //$this->response->end($this->redis->get('key'));
        //\Yaf\Registry::get('redis_pool')->put($this->redis);

        $value = RedisFactory::login()->setKey('setKey', '123');
        $this->response->end($value);
    }

    /**
     * 在SWOOLE_BASE模式下用ab压测以下两种代码方式输出的效果，会发现协程模式提速很快
     * User: hanhyu
     * Date: 19-7-25
     * Time: 下午4:18
     */
    public function testCoAction(): void
    {
        //在开启Swoole\Runtime::enableCoroutine()模式下
        print_r('123');
        sleep(1);
        print_r('456' . PHP_EOL);
        $this->response->end();
    }


}
