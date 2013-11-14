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
	<script src="js/jquery.min.js" ></script>
	<script src="js/underscore-min.js" ></script>
	<script src="js/bootstrap.min.js" ></script>
	<script src="js/main.min.js" ></script>
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
	#optlist a.btn{
		text-align: left;
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
<div id="errorModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 id="modalTitle" class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <p><h3><span id="errorMsgHere" class="text-warning"></span><h3></p>
      </div>
      <div class="modal-footer text-center">
        <button type="button" class="btn btn-default" data-dismiss="modal">確定</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<form method="post" action="counter.php" class="form-horizontal" role="form" onsubmit="return isValidForm(this);">
<input type="hidden" name="patient_id">
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
<!-- 				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button> -->
				<a class="navbar-brand">長庚問卷調查系統</a>
			</div>
<!-- 			<div class="collapse navbar-collapse navbar-ex1-collapse pull-right">
				<ul class="nav navbar-nav">
					<li><a href="./">回首頁</a></li>
				</ul>
			</div> -->
		</div>
	</nav>
	<div class="container">
		<h4 style="margin:0;">
			<span id="questionnaire_name"></span> <small class="pull-right text-info">病患：<span id="p_id"></span> <span id="p_name"></span></small>
		</h4>
	</div>

	<div id="door" class="container">
		<br>
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="form-group">
					<label class="text-muted" for="p_id">病歷號</label>
					<input type="text" class="form-control" style="width:100%;" id="p_id" name="p_id" onKeyup="findPatient(this.value);">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="form-group">
					<label class="text-muted" for="p_name">姓名</label>
					<input type="text" class="form-control" style="width:100%;" id="p_name" name="p_name" readOnly>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="form-group">
					<label class="text-muted" for="p_name">體重(kg)</label>
					<input type="text" class="form-control" style="width:auto;" id="p_weight" name="p_weight">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="text-center">
					<a id="startQ" onclick="startQuest();" class="btn btn-default">開始</a>
				</div>
			</div>
		</div>
	</div>

	<div id="paper" class="container" style="display:none;">
		<div id="foreword"></div>
		<h3><span id="q_title"></span><span id="q_no" class="text-warning pull-right"></span></h3>
		<hr/>
		<!-- <div id="optlist" class="btn-group-vertical btn-group-lg"></div> -->
		<div id="optlist" class="btn-group btn-group-lg"></div>
		<hr/>
		<a id="prevQ" onclick="setQuest(-1);" class="btn btn-default"><i class="icon icon-chevron-left"></i> 上一題</a>
		<a id="nextQ" onclick="setQuest(1);" class="btn btn-default pull-right">下一題 <i class="icon icon-chevron-right"></i></a>
	</div>
	<div id="send" class="text-center" style="display: none;">
		<!-- <button class="btn btn-lg btn-default"><i class="icon icon-remove"></i>  取　消</button> -->
		<button id="submitButton" class="btn btn-success"><i class="icon icon-ok"></i>  送　出</button>
	</div>
<script>
var q_id,
	q_no = 0,
	sub_q_no = -1,
	answer = [],
	quizzes,
	sent=false;

$(function(){
	var url_params={};
	$.each(location.search.substr(1).split('&'),function(){
		var p=this.split('=');
		url_params[p[0]]=p[1];
	});
	q_id=url_params['questionnaire'];
	$.getJSON('questionnaireMap.php',{q_id:q_id},function(data){
		quizzes=data;
		initQuestionnaire();
	});
});
</script>
</form>
</body>
</html>