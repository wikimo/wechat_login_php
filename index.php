<?php
session_start();
$_SESSION["access_token"] = "none";
$_SESSION["refresh_token"] = 'none';
$_SESSION["scope"] = 'none';
$_SESSION["openid"] = 'none';
$_SESSION["code"] = '';
$_SESSION["state"] = '';
?>

<!DOCTYPE html>
<html>
<head>
<title>Wechat Web Login Demo</title>
<meta charset="utf-8">
<script src="http://res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js"></script>

</head>
<body>
<h1>Wechat Qrcode Login</h1>
<div id="login_container"></div>

<p><?=('http://dev.cmcm.us/index.php')?></p>

<script type="text/javascript">
var obj = new WxLogin({

    id:"login_container", 
    appid: "wx70a2b1aa7fed4cc9", 
    scope: "snsapi_login", 
    redirect_uri:"<?=UrlEncode('http://dev.cmcm.us/main.php')?>",
    state: "<?=md5('cmcmus');?>",
    style: "black",
    href: ""//custom link of css
});                  
</script>
</body>
</html>