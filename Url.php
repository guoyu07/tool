<?php
namespace pakey\tool;
/**
 * Class Tool_Url
 * Url处理相关
 */
class Url{

    /**
     * 获取当前URL
     *
     * @return string
     */
    public static function weixin()
    {
        $url=self::this();
        if(strpos($url,'#')){
            $url=explode('#',$url)['0'];
        }
        return $url;
    }

    public static function current() {
        if(PHP_SAPI=='cli'){
            return 'cli';
        }
        if(strpos($_SERVER['REQUEST_URI'],'http://')===0){
            return $_SERVER['REQUEST_URI'];
        }
        $protocol = (!empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
            || $_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';

        if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])){
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        }else{
            $host = $_SERVER['HTTP_HOST'];
        }
        $url=$protocol.$host.$_SERVER['REQUEST_URI'];
        return $url;
    }
}