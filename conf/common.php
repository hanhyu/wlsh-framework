<?php
//公共环境配置
return [

    'application' => [
        'directory'  => ROOT_PATH . '/application',
        'modules'    => 'Index,Task,Finish,Ws,Tcp,Close,System',
        'dispatcher' => [
            'defaultController' => 'login',
        ],
    ],

    'token' => [
        'encryptKey' => 'WLSH1707181007time0507cs5TKey=',
        'encryptIv'  => 'Iv&li7lib8201811070q119==',
        //接口登录过期时间，默认8小时
        'expTime'    => 28800,
    ],

    'deny_http_module' => ['task', 'finish', 'ws', 'tcp', 'close'],

    'version' => 'v2.0',

    'before_url' => 'https://www.wlsh.xyz',

    'sign' => [
        'flag' => true,

        'prv_key' => '-----BEGIN PRIVATE KEY-----
MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQClZisT/47ZQcRM
0I6kx042+fJqStrY3cywYQo6o3F3pfU+ZxE/KyWJpBWNaVpqQHya0mkx5nM39yud
yYbKd1J8m6oLRPuhYzXXKp2Sn8BdUxnw8dquuD/btOOTEaMA1D1fvAJJCaCCXHnW
eGM05lNXewJ4v3QV6bGX1DW8dVcLeFxnb0nh5awt5ASwKMQaAdOI7IAMK0ZvJaKN
T5F98xjDhO27SClEY/8oXpoS1ek6og9W+Vr8ZNSyCN4wnmB5jGV1L7EomLLeLUmo
NrHWt92Z5Kbt5FZr4qKqncgVhdvTI2FItGRjX9GZvZbHrFqm3Puzf6ACistDwgLK
OppLegoLAgMBAAECggEBAJr1VXau/05cZ2TwIDQQ5h5rnconx1FWu+ajg8ncRvop
6dJFDct4yPpjWC3cfnD9acBDUXsGxPux/pMds8MMd6N206ErR/Sz1kV4D5jHQpos
uSkb7h2zUWCt4GhxJZ4pOjbvFdUHx/FaYmuk7o0pHPvgqzglZsUA5PmyP4YJCTwc
Ta/YSZmNoPNAhekrs4zcMFpY7GNP1xpW10ZKiXNv3OJMC7oMnpjprsmTmu8Eb+Mp
nln00YMyPyqBe/UF1ebbDUfiee02xWItNVoKupHePH41l5NxUQHjzxYtQL/Jdtp4
JhEQoU6yI3hP47Adiaslp7K8kwwe7NpcZHr4ITJhVQECgYEA1/70OPEmFuksAJtx
Kg7GiBrRRkJpg9li+NEg8BRLZHL4DoldbCTwd2JxTf1V0PnTFG3SkHyLlgYoWYJ/
AV6Oi+AxvG/RyEAvTExsb7J0VH/1kzwIQkAd3zZZCXN6sUyMU2eBCqlNAseyA3Da
1RJkG7iN3VuWOXZPZuUMu+XoAnsCgYEAxAhChfnB6FGe/qub1KV3aX01+1Rt9k9B
o5OMU2/jEzNk4ywUEB7ZiAbCWbcbs1sWBSLGnSt6X4onAQArIjaBaxkLsM/sKcmk
oitCFXXHuntJ4OyPk72cMx5cdu6Vj9eMAu8l/6M+hv8xpnwjO/ZnyQfErG9Pl2ab
3733v93OCbECgYEAt1Cda3pWzlkEzFsgdwZlPnwsIsoYjRtBYVTz9G308oKUvpmc
nTzYjSoSaZNZcuukVpFChPf+68u8EQOs0Le0mUgkTf5E+ARpYAL4gO/exRx4ioYH
qFqMP7X7aQcGGVWTPvH4VdkMEBD1pu/v+agLKG/Ajx6853Xeu8Anx4y/FOMCgYEA
vMaTgT8ly1omeWq60slROAW64T/J8OMb+7EP9h2OF6RraBS/cVKz+rk/mXOqukph
5yo5B4uh+bUpoF4QK48/i+n/hxKDgy5KW1y6872LS/qEB5TnIsf6ToPTWTiiUHWq
v/yapBYJFalUn9V1fL3p7f6MF93NUKggPRNMLLklO9ECgYBeEze1atP0u9lBNEJp
CM4+49c8PARuqVCEwduIhPh6zJaL9BHqQFJh1i5hwgvwzZ6fWOCoiN2VUIhgpfSo
X4zjw9RNmL6nNXB3HNgM6MqY+toW+F28gogEG1N66Q04VDqM1Ha0wz3bfnDis7/k
T8iFUed1twItVqBt54Rss1UNVA==
-----END PRIVATE KEY-----',

        'pub_key' => '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApWYrE/+O2UHETNCOpMdO
Nvnyakra2N3MsGEKOqNxd6X1PmcRPysliaQVjWlaakB8mtJpMeZzN/crncmGyndS
fJuqC0T7oWM11yqdkp/AXVMZ8PHarrg/27TjkxGjANQ9X7wCSQmgglx51nhjNOZT
V3sCeL90Femxl9Q1vHVXC3hcZ29J4eWsLeQEsCjEGgHTiOyADCtGbyWijU+RffMY
w4Ttu0gpRGP/KF6aEtXpOqIPVvla/GTUsgjeMJ5geYxldS+xKJiy3i1JqDax1rfd
meSm7eRWa+Kiqp3IFYXb0yNhSLRkY1/Rmb2Wx6xaptz7s3+gAorLQ8ICyjqaS3oK
CwIDAQAB
-----END PUBLIC KEY-----',
    ],

];
