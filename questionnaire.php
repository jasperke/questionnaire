<?php
session_start();
require_once('rpc/questionnaireMap.php');

if(!isset($_SESSION['admin'])||strcmp($_SESSION['admin'],'changgung')!=0){ // 未登入
	header('Location: ./login.php');
	exit;
}else{
	if(!isset($questionnaire)||!isset($questionnaireMap[CURRENT_VERSION][$questionnaire])){
		header('Location: ./');
		exit;
	}
}
?>

<!DOCTYPE html>
<html lang="zh-tw">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="user-scalable=no, width=device-width" />
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
		padding: 24px 14px;
		margin: 6px auto;
	}
	#optlist a.btn-default:hover,
	#optlist a.btn-default.active {
		background: #FFFFFF;
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
	#foreword {padding-top:26px;}
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
    </div>
  </div>
</div>

<div id="confirmModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4>注意</h4>
		</div>
		<div class="modal-body">
			<p><h4>體重與上次填問卷時的體重差異超過3公斤！<br>確定正確？</h4></p>
		</div>
		<div class="modal-footer text-center">
			<button type="button" class="btn" data-dismiss="modal">重填</button>
			<button id="weightOkButton" type="button" class="btn btn-primary" data-dismiss="ok">確定</button>
		</div>
    </div>
  </div>
</div>

<form method="post" action="rpc/counter.php" class="form-horizontal" role="form" onsubmit="return isValidForm(this);">
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
			<span id="questionnaire_name"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="q_no" class="text-warning invisible"></span> <span class="pull-right text-info"><span id="span_p_name"></span> <span id="span_p_id"></span>&nbsp;&nbsp;&nbsp;&nbsp;醫師：<?php echo  $_SESSION['doctor'];?></span>
		</h4>
	</div>

	<div id="door" class="container">
		<br>
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
				<div class="form-group">
					<label class="text-muted" for="p_id" style="font-size:24px;">病歷號</label>
					<input type="text" class="form-control" style="width:100%;" id="p_id" name="p_id" autocomplete="off" onKeyup="findPatient(this.value);">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
				<div class="form-group">
					<label class="text-muted" for="p_name" style="font-size:24px;">姓名</label>
					<input type="text" class="form-control" style="width:100%;" id="p_name" name="p_name" readOnly>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
				<div class="form-group">
					<input type="hidden" name="p_last_weight">
					<label class="text-muted" for="p_weight" style="font-size:24px;">體重(kg)</label>
					<input type="text" class="form-control" style="width:auto;" id="p_weight" name="p_weight">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
				<div class="form-group">
					<label class="text-muted" for="p_base" style="font-size:24px;">生活基本資料</label>
					&nbsp;<input type="checkbox" id="p_base" name="p_base" checked>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
				<div class="text-center" style="margin-top:24px;">
					<a id="startQ" onclick="startQuest();"  style="font-size:20px;" class="btn btn-default">開始</a>
				</div>
			</div>
		</div>
	</div>

	<div id="paper" class="container" style="display:none;">
		<div id="quizKind" style="display:none;"><!-- 通用選項格式題 -->
			<div id="foreword"></div>
			<h3><span id="q_title"></span></h3>
			<hr/>
			<div id="fore_img" style="padding-left:10px;"></div>
			<div id="optlist" class="btn-group-vertical btn-group-lg"></div>
			<hr/>
		</div>
		<div id="quizKind1" style="display:none;"><!-- 刻度尺格式題 -->
			<div>&nbsp;</div>
			<div class="row">
				<div class="col-sm-8">
					<h3><span id="q_title_kind1"></span></h3>
					<hr/>
					<div class="col-sm-4">
						<a onclick="setQuest(-1);" class="btn btn-default"><i class="glyphicon glyphicon-chevron-left"></i> 上一題</a>
					</div>
					<div class="col-sm-4 col-sm-offset-4">
						<a onclick="setQuest(1);" class="btn btn-default pull-right">確定<!-- <i class="glyphicon glyphicon-chevron-right"></i> --></a>
					</div>

				</div>
				<div class="col-sm-4">
					<div style="text-align:center;">想像中最好的健康狀況</div>
					<div id="healthRuler" style="width:200px; height:680px; margin:0px auto; background-image:url('./images/health_ruler.gif'); background-repeat:no-repeat; background-position:center; cursor:pointer;">
						<div id="healthMark" style="position:relative; top:0px; left:0px; width:200px; height:80px; visibility:hidden; cursor:pointer; background-image:url('./images/health_mark.png'); background-repeat:no-repeat; background-position:center; padding-left:140px; font-size: 22px; font-family: arial;"></div>
					</div>
					<div style="text-align:center;">想像中最差的健康狀況</div>
				</div>
			</div>
		</div>

		<div id="commonQuizSwitcher" class="row">
			<div class="col-sm-3">
				<a id="prevQ" onclick="setQuest(-1);" class="btn btn-default"><i class="glyphicon glyphicon-chevron-left"></i> 上一題</a>
			</div>
			<div class="col-sm-2 col-sm-offset-2">
				<button id="send" class="btn btn-success text-center"><i class="icon icon-ok"></i>  送　出</button>
			</div>
			<div class="col-sm-3 col-sm-offset-2">
				<a id="nextQ" onclick="setQuest(1);" class="btn btn-default pull-right">下一題 <i class="glyphicon glyphicon-chevron-right"></i></a>
			</div>
		</div>
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
	$.getJSON('rpc/questionnaireMap.php',{q_id:q_id},function(data){
		quizzes=data;
		initQuestionnaire();
	});
	if(q_id=='HN.COM'){
		var healthCheckbox=$('#p_base');
		healthCheckbox.get(0).checked=false;
		healthCheckbox.parents('.row').hide();
	}
	$('#healthMark').on("touchstart touchmove touchend", touchHandler)
		.on('mousedown', function (event) {
			var healthRuler=$('#healthRuler'),
				healthMark=$('#healthMark'),
				totalHeight=healthRuler.height()-80,
				pos=healthRuler.offset(),
				top=parseInt(event.pageY-pos.top,10)-40;
			if(top<0)
				top=0;
			if(top>(totalHeight))
				top=totalHeight;
			healthRuler.data({active:1, top:pos.top});
			healthMark.css({top:top+'px'});

			var score=Math.round((totalHeight-top)*100/totalHeight);
			healthMark.html(score);
			keepAnswer(q_no, -1, score);
		})
		.on('mouseup mouseout', function () {
			var healthRuler=$('#healthRuler');
			healthRuler.data({active:0});
		})
		.on('mousemove', function (event) {
			var healthRuler=$('#healthRuler'),
				healthMark=$('#healthMark'),
				status=healthRuler.data(),
				totalHeight=healthRuler.height()-80,
				top=parseInt(event.pageY-status.top,10)-40;
			if(top<0)
				top=0;
			if(top>(totalHeight))
				top=totalHeight;
			if(status.active){
				healthMark.css({top:top+'px'});

				var score=Math.round((totalHeight-top)*100/totalHeight);
				healthMark.html(score);
				keepAnswer(q_no, -1, score);
			}
		});
	$('#healthRuler').on("mousedown", function (event) {
		var healthRuler=$('#healthRuler'),
			healthMark=$('#healthMark').css({visibility:'visible'}),
			pos=healthRuler.offset(),
			totalHeight=healthRuler.height()-80,
			top=parseInt(event.pageY-pos.top,10)-40;
		if(top<0)
			top=0;
		if(top>totalHeight)
			top=totalHeight;
		healthRuler.data({active:1, top:pos.top});
		healthMark.css({top:top+'px'});

		var score=Math.round((totalHeight-top)*100/totalHeight);
		healthMark.html(score);
		keepAnswer(q_no, -1, score);
	});
});
</script>
</form>
</body>
</html>