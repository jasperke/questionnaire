<?
session_start();

if(!isset($_SESSION['admin'])||strcmp($_SESSION['admin'],'changgung')!=0){ // 未登入
	header('Location: ./login.php');
	exit;
}
?>
<!DOCTYPE html>
<html lang="zh-tw">
<head>
	<meta charset="utf-8" />
	<title>問卷調查</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<script type="text/javascript" src="js/jquery.min.js" ></script>
	<script type="text/javascript" src="js/bootstrap.min.js" ></script>
</head>
<body>
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.html">長庚問卷調查系統</a>
			</div>
			<div class="collapse navbar-collapse navbar-ex1-collapse pull-right">
				<ul class="nav navbar-nav">
					<li><a href="login.php?logout=1">登出</a></li>
				</ul>
			</div>
			<div class="collapse navbar-collapse navbar-ex1-collapse pull-right">
				<ul class="nav navbar-nav">
					<li><a href="index.php">主選單</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div id="mainMenu" class="container">
		<h3>請選擇：</h3>
		<div class="list-group">
			<a id="patient" href="userList.php" class="list-group-item">患者基本資料 <i class="icon icon-chevron-right pull-right"></i></a>
			<a id="questionnaire" herf="#" class="list-group-item">填寫問卷 <i class="icon icon-chevron-right pull-right"></i></a>
			<a id="manager" href="#" class="list-group-item">問卷結果 <i class="icon icon-chevron-right pull-right"></i></a>
		</div>
	</div>
	<div id="questionnaireSelector" class="container">
		<h3>請選擇問卷：</h3>
		<div class="list-group">
			<a href="questionnaire.php?questionnaire=FACT-B" class="list-group-item" target="_blank">
				FACT - B <i class="icon icon-chevron-right pull-right"></i>
			</a>
			<a href="questionnaire.php?questionnaire=FACT-ECO" class="list-group-item" target="_blank">
				FACT - ECO <i class="icon icon-chevron-right pull-right"></i>
			</a>
			<a href="questionnaire.php?questionnaire=FACT-HN-X" class="list-group-item" target="_blank">
				FACT - HN-X <i class="icon icon-chevron-right pull-right"></i>
			</a>
			<a href="questionnaire.php?questionnaire=HN.COM" class="list-group-item" target="_blank">
				HN.COM <i class="icon icon-chevron-right pull-right"></i>
			</a>
		</div>
	</div>
<script>
$(function(){
	$('#questionnaireSelector').hide();
	$('#questionnaire').on('click',function(){
		$('#mainMenu').hide();
		$('#questionnaireSelector').show();
	})
});
</script>
</body>
</html>