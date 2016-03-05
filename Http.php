<?php

namespace pakey\tool;

class Http {

    /**
     * @param       $url
     * @param array $params
     * @param array $header
     * @param array $option
     * @return bool|mixed
     */
    public static function get($url,$params=[],$header=[],$option=[])
    {
        return self::request($url,$params,'GET',$header,$option);

    }

    public static function post($url,$params=[],$header=[],$option=[])
    {
        return self::request($url,$params,'POST',$header,$option);

    }

    public static function request($url, $params = [], $method = 'GET', $header = [], $option = [])
    {

        $opts = array(
            CURLOPT_TIMEOUT        => isset($option['timeout'])?$option['timeout']:5,
            CURLOPT_CONNECTTIMEOUT => isset($option['connecttimeout'])?$option['connecttimeout']:5,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HEADER         => false,
            CURLOPT_NOSIGNAL       => 1,
            CURLOPT_ENCODING       => 'gzip, deflate',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        );

        if(isset($header['referer'])){
            $opts[CURLOPT_REFERER]=$header['referer'];
            unset($header['referer']);
        }

        if(isset($header['cookie'])){
            $opts[CURLOPT_COOKIE]=$header['cookie'];
            unset($header['cookie']);
        }

        if(isset($header['useragent'])){
            $opts[CURLOPT_USERAGENT]=$header['useragent'];
            unset($header['useragent']);
        }

        if(isset($header['showheader'])){
            $opts[CURLOPT_HEADER]=true;
            unset($header['showheader']);
        }

        if(isset($option['proxy'])){
            $opts[CURLOPT_PROXY]=$option['proxy'];
            $opts[CURLOPT_PROXYPORT]=$option['proxyport'];
        }

        if(isset($option['sslcerttype'])){
            $opts[CURLOPT_SSLCERT]=$option['sslcert'];
            $opts[CURLOPT_SSLCERTTYPE]=$option['sslcertype'];
        }

        if(isset($option['sslkeytype'])){
            $opts[CURLOPT_SSLKEY]=$option['sslkey'];
            $opts[CURLOPT_SSLKEYTYPE]=$option['sslkeytype'];
        }

        if(!empty($header)){
            $opts[CURLOPT_HTTPHEADER]=$header;
        }

        //补充配置
        foreach ($option as $k => $v) {
            $opts[$k] = $v;
        }
        //安全模式
        if (ini_get("safe_mode") || ini_get('open_basedir')) {
            unset($opts[CURLOPT_FOLLOWLOCATION]);
        }
        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                if ($params) {
                    if (strpos($url, '?')) {
                        $url .= '&' . $params;
                    } else {
                        $url .= '?' . $params;
                    }
                }
                $opts[CURLOPT_URL] = $url;
                break;
            case 'POST':
                //判断是否传输文件
                $opts[CURLOPT_URL]        = $url;
                $opts[CURLOPT_POST]       = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                exit('不支持的请求方式！');
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($error && $errno !== 28) {
            echo "call faild, errorCode:$errno, errorMsg:$error\n";
            return false;
        }
        return $data;
    }
}