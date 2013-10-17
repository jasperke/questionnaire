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
	<script type="text/javascript" src="js/jquery.js" ></script>
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
					<li><a href="list.php">問卷列表</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container">
		<div>請選擇問卷：</div>
		<div class="list-group">
			<a href="questionnaire.php?questionnaire=FACT-B" class="list-group-item">
				FACT - B <i class="icon icon-chevron-right pull-right"></i>
			</a>
			<a href="questionnaire.php?questionnaire=FACT-ECO" class="list-group-item">
				FACT - ECO <i class="icon icon-chevron-right pull-right"></i>
			</a>
			<a href="questionnaire.php?questionnaire=FACT-HN-X" class="list-group-item">
				FACT - HN-X <i class="icon icon-chevron-right pull-right"></i>
			</a>
			<a href="questionnaire.php?questionnaire=HN.COM" class="list-group-item">
				HN.COM <i class="icon icon-chevron-right pull-right"></i>
			</a>
		</div>
	</div>
</body>
</html>