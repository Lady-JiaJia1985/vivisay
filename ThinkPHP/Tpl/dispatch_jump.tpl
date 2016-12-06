<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
<link href="//cdn.bootcss.com/bootstrap/3.0.2/css/bootstrap.min.css" rel="stylesheet">
<style type="text/css">
	.system-message{ margin: 0 auto; padding: 6rem 3rem; max-width: 60%; }
	.panel-body p { line-height: 1.8rem; }
	.msg { font-size: 1.4em; }
</style>
</head>
<body>
<div class="system-message">
<?php if(isset($message)){?>
	<div class="panel panel-success">
		<div class="panel-heading">
			<h3 class="panel-title">跳转提示</h3>
		</div>
		<div class="panel-body">
			<p class="msg"><?php echo($message); ?></p>
			<p class="jump">
				页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>
			</p>
		</div>
	</div>
<?php }else{?>
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h3 class="panel-title">跳转提示</h3>
		</div>
		<div class="panel-body">
			<p class="msg"><?php echo($error); ?></p>
			<p class="jump">
				页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>
			</p>
		</div>
	</div>
<?php }?>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
