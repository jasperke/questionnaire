<?php
session_start();
require_once('questionnaireMap.php');

if(!isset($_SESSION['admin'])||strcmp($_SESSION['admin'],'changgung')!=0){ // 未登入
	header('Location: ./login.php');
	exit;
}else{
	if(!isset($questionnaire)||!isset($questionnaireMap[$questionnaire])){
		header('Location: ./');
		exit;
	}
}
?>

<!DOCTYPE html>
<html lang="zh-tw">
<head>
	<meta charset="utf-8" />
	<title>問卷調查</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<script type="text/javascript" src="js/jquery.min.js" ></script>
	<script type="text/javascript" src="js/underscore-min.js" ></script>
	<script type="text/javascript" src="js/bootstrap.min.js" ></script>
	<script type="text/javascript" src="js/main.min.js" ></script>
	<style type="text/css">
	* {
		font-family: "微軟正黑體";
	}
	html, body, h3 small, h4 small {
		font-size: 17px;
		font-family: "微軟正黑體";
	}
	#optlist {
		width: 90%;
		margin: 10px 5%;
	}
	#optlist a.btn-default:hover,
	#optlist a.btn-default.active {
		background: #F0F0F0;
	}
	#optlist a.text-danger,
	#optlist a.text-danger span {
		color: #B94A48;
	}
	#optlist a span {
		color: #999;
		vertical-align: middle;
		margin-right: 10px;
	}
	</style>
</head>
<body>
<form method="post" action="counter.php" class="form-inline" role="form" onsubmit="return isValidForm(this);">
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.html">問卷調查</a>
			</div>
			<div class="collapse navbar-collapse navbar-ex1-collapse pull-right">
				<ul class="nav navbar-nav">
					<li><a href="./">回首頁</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container">
		<h3 style="margin:0;">
			<span id="questionnaire_name"></span> <small class="pull-right text-info">病患：<span id="p_id"></span> <span id="p_name"></span></small>
		</h3>
	</div>

	<div id="door" class="container paper">
		<div class="form-group" style="margin-right:20px;">
			<label class="text-muted" for="p_id">病歷號</label>
			<input type="text" class="form-control" style="width:auto;" id="p_id" name="p_id">
		</div>
		<div class="form-group" style="margin-right:20px;">
			<label class="text-muted" for="p_name">姓名</label>
			<input type="text" class="form-control" style="width:auto;" id="p_name" name="p_name">
		</div>
		<div class="form-group" style="margin-right:20px;">
			<label class="text-muted" for="p_name">體重</label>
			<input type="text" class="form-control" style="width:auto;" id="p_weight" name="p_weight"> kg
		</div>
		<br><br>
		<a id="startQ" onclick="startQuest();" class="btn btn-lg btn-default">開始</a>
	</div>

	<div id="paper" class="container paper" style="display:none">
		<span id="foreword"></span><br>
		<h1><span id="q_title"></span><span id="q_no" class="text-warning pull-right"></span></h1>
		<hr/>
		<div id="optlist" class="btn-group-vertical btn-group-lg"></div>
		<hr/>
		<a id="prevQ" onclick="setQuest(-1);" class="btn btn-lg btn-default"><i class="icon icon-chevron-left"></i> 上一題</a>
		<a id="nextQ" onclick="setQuest(1);" class="btn btn-lg btn-default pull-right">下一題 <i class="icon icon-chevron-right"></i></a>
	</div>
	<br/>
	<center id="send" style="display: none;">
		<!-- <button class="btn btn-lg btn-default"><i class="icon icon-remove"></i>  取　消</button> -->
		<button id="submitButton" class="btn btn-lg btn-success"><i class="icon icon-ok"></i>  送　出</button>
	</center>
<script type="text/javascript">
var q_id='<? echo $questionnaire;?>',
	q_no = 0,
	sub_q_no = -1,
	answer = [],
	quizzes=<? echo json_encode($questionnaireMap[$questionnaire]);?>;

$(function(){
	$('#questionnaire_name').text(q_id);

	setQuest(0);
	$("#optlist").on("click", "a.btn", function(event) {
		$(this).addClass("active text-danger")
		.find("span").removeClass("icon-unchecked").addClass("icon-check").end()
		.siblings("a.active").removeClass("active").removeClass("text-danger")
		.find("span").removeClass("icon-check").addClass("icon-unchecked");

		var sub_q_no=$(this).data('sub_q_no'),
			q_no=$(this).data('q_no'),
			val=$(this).data('val');
		keepAnswer(q_no,sub_q_no,val); // 暫存答案至answer

		if(event.originalEvent&&q_no<quizzes.length-1){ // user作答完, 自動換下一題
														// 切換上下題時, 標示已填答案也是trigger click event, 但不必換題
			setTimeout("setQuest(1);",300);
		}
	});
});
</script>
</form>
</body>
</html>