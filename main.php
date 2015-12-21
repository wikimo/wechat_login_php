<?php
session_start();
$code = isset($_GET['code']) ? $_GET['code'] : '';
$state = isset($_GET['state']) ? $_GET['state'] : '';

$_SESSION["code"] = $code;
$_SESSION["state"] = $state;

$appid = '';//
$secret = '';//

/**
//validate if illegal，redirect
*/
if ($code!=''&&$state===md5('cmcmus')) {
    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
    //echo $url;
}else{

    header("Location: http://".$_SERVER['HTTP_HOST']."/weixinsdk/index.php"); 
    //code not executed, if redirect
    die();
}

/**
* @param curlAPI send request
* @param $url 
*/
function curlAPI($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //solve SSL certificate problem: unable to get local issuer certificate
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = json_decode(curl_exec($ch), true);
    if ($result === FALSE) {
        return "cURL Error: " . curl_error($ch);
        curl_close($ch);
        die();
    }
    curl_close($ch);
    return $result;
}
/**
* @param debugPre
*/
function debugPre ($a){
    echo "<pre>";
    var_dump($a);
    echo "</pre>";
}

//encryption
function getAwardCheckCode() {
    $time = time();
    $key = "12345";
    $secret = md5($time.$key);
    return "&time=".$time."&secret=".$secret;
}

function get_password( $length = 8 ){
    $str = substr(md5(time()), 0, 6);
    return $str;
}
/**
//get access_token
*/
$outputAPI = curlAPI($url);
if (isset($outputAPI["access_token"])) {
    //setting session
    $_SESSION["access_token"] = $outputAPI['access_token'];
    $_SESSION["refresh_token"] = $outputAPI['refresh_token'];
    $_SESSION["scope"] = $outputAPI['scope'];
    $_SESSION["openid"] = $outputAPI['openid'];
    
} else{
    //debugPre($outputAPI);
}
/**
// refresh_token
*/

$refresh_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$appid.'&grant_type=refresh_token&refresh_token='.$_SESSION["refresh_token"];
$refresh_API = curlAPI($refresh_url);

if (isset($refresh_API["access_token"])) {
    //updated session token
    $_SESSION["access_token"] = $refresh_API['access_token'];
    $_SESSION["refresh_token"] = $refresh_API['refresh_token'];
    $_SESSION["scope"] = $refresh_API['scope'];
    $_SESSION["openid"] = $refresh_API['openid'];

}else{
    //debugPre($refresh_API);
}
/**
//validate access_token
*/
$verify_url = 'https://api.weixin.qq.com/sns/auth?access_token='.$_SESSION["access_token"].'&openid='.$_SESSION["openid"];
$verify_API = curlAPI($verify_url);


if ($verify_API['errmsg']==='ok') {
    /**
    //get user info
    */
    $user_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$_SESSION["access_token"].'&openid='.$_SESSION["openid"];
    $user_API = curlAPI($user_url);
    if (isset($user_API['nickname'])) {
        $openid     = $user_API['openid'];
        $nickname   = $user_API['nickname'];
        $sex        = $user_API['sex'];
        $province   = $user_API['province'];
        $city       = $user_API['city'];
        $country    = $user_API['country'];
        $headimgurl = $user_API['headimgurl'];
        $privilege  = $user_API['privilege'];
        $unionid    = $user_API['unionid'];

    }else{
        debugPre($user_API);
    }

}else{
    debugPre($verify_API);
    die();
}
?>

<html>
<head>
<title>IF User Login</title>
<meta charset="utf-8">
</head>
<body>

<h1>Hi：<?=$nickname?></h1>
<p><img src="<?=$headimgurl?>"> <?=$sex=='1'?'Man':'Women'?></p>
<p data-id="<?=$unionid?>">From：<?=$country.$province.$city?></p>
<p data-openid="<?=$openid?>"><?=$privilege?></p>

</body>
</html>