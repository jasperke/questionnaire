<?php
session_start();
require_once('questionnaireMap.php');

if(!isset($_SESSION['admin'])||strcmp($_SESSION['admin'],'changgung')!=0){ // 未登入
  header('Location: ./login.php');
  exit;
}


require_once('koala.Utility.php');
require_once('common.Utility.php');
require_once('rpc.Utility2.php');
//require_once('questionnaireMap.php');
require_once('questionnaireUtility.php');
define("CFG_FN", "/usr/local/koala/config.ini");

$group_id=2; // OwnerID
$dbid=2;
$out=array(array(0,''));

$SiteLoginUID=kwut2_readini(CFG_FN,"KOALA","SiteUID");
$SiteLoginPWD=kwut2_readini(CFG_FN,"KOALA","SitePWD");
$db=kwcr2_mapdb('CyberSite',$SiteLoginUID,$SiteLoginPWD);
if($db!=0){
	$gukey=global_unique_key_decode($id);
	$s="select Questionnaire,No,Answer,Score,Version,Name,Weight,CreateTime from MUST_Questionnaire where OwnerID=? and CreateTime=? and RandNum=?";
	$p=array($group_id,$gukey[0],$gukey[1]);
	$r=read_one_record($db, $s, $p);
	if($r===false){
		$out[0]=array(900,"讀取資料失敗！".kwcr2_geterrormsg($db, 1));
	}else if(!isset($r)){
		$out[0]=array(901,"查無符合資料！".kwcr2_geterrormsg($db, 1));
	}else{
		$out[]=$r;
		$questionnaire=$r[0];
	}
}else{
	$out[0]=array(900,"資料庫連結失敗！");
}

?>
<!DOCTYPE html>
<html lang="zh-tw">
<head>
	<meta charset="utf-8" />
	<title>問卷調查</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<script type="text/javascript" src="js/jquery.js" ></script>
	<script type="text/javascript" src="js/underscore-min.js" ></script>
	<script type="text/javascript" src="js/bootstrap.min.js" ></script>
	<script type="text/javascript" src="js/quizPool.js" ></script>
<script>
var	data=<? echo QUtillity::decodeUnicodeString(json_encode($out)); ?>,
	questionnaire='<? echo isset($questionnaire)?$questionnaire:"HN.COM"; ?>',
	quizzes=<? echo json_encode($questionnaireMap[isset($questionnaire)?$questionnaire:'HN.COM']); ?>,
	quiz_view;

$(function(){
	if(data[0][0]!=0){
		$('#questionnaire_name').html('<font color="#ff0000">錯誤！<br><br>錯誤代碼：'+data[0][0]+'<br>錯誤訊息：'+data[0][1]+'</font>');
	}else{
		var sheet_here=$('#sheet_here'),
			answer=$.parseJSON(data[1][2]);
		quiz_view=_.template($("#row_template").html());
		$('#questionnaire_name').html(questionnaire);
		$('#p_name').html('病患：'+data[1][5]+'（'+data[1][1]+'）');
		$('#time').html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;問卷時間：'+data[1][7].substr(0,16));
		if(data[1][3]!=='')
			$('#score').html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;總分：'+data[1][3]);

		$.each(quizzes,function(i,n){
			var quiz,
				ans=answer[i].toString().split(':'),
				n=n.split(':');

			quiz=quizPool[n[0]];
			if(quiz.foreword!==undefined)
				sheet_here.append('<hr/><small class="text-danger">'+quiz.foreword+'</small>');
			sheet_here.append(quiz_view({quiz:quiz.quiz,name:n,options:quiz.options||commonOptions,idx:i+1,ans:ans[0]}));

			if(n.length>1){ // 有子問題群
				var sub_ans,
					sub_quizzes=n[parseInt(ans[0],10)+1]; // 依主問題回答找出對映的子問題群
				if(sub_quizzes!==undefined&&sub_quizzes!==''){
					sub_quizzes=sub_quizzes.split(',');
					sub_ans=ans[1].split(',');
					$.each(sub_quizzes,function(sub_i,sub_n){
						var sub_quiz=quizPool[sub_n];
						sheet_here.append(quiz_view({quiz:sub_quiz.quiz,name:sub_n,options:sub_quiz.options||commonOptions,idx:(i+1)+'-'+(sub_i+1),ans:sub_ans[sub_i]}));
					})
				}
			}
		});

		$('body').on('click','input:radio',function(event){
			console.log('radio clicked');
			event.preventDefault();
		});
	}
});
</script>
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
				<a class="navbar-brand" href="index.html">問卷調查</a>
			</div>
			<div class="collapse navbar-collapse navbar-ex1-collapse pull-right">
				<ul class="nav navbar-nav">
					<li><a href="list.php">回列表</a></li>
					<li><a href="./">回首頁</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container paper" id="sheet_here">
		<h3 style="margin:0;"><span id="questionnaire_name"></span> <small class="pull-right text-info"><span id="p_name"></span>
		<span id="time"></span><span id="score"></span></small></h3>
	</div>

<script type="text/template" id="row_template">
	<hr/><h4><%= idx %>. <%= quiz %></h4>
	<div class="col-lg-12">
	<%_.forEach(options, function (o,i) {%>
		<label class="checkbox-inline"><input type="radio" <%= (ans==i)?'checked':'' %> name="<%= name %>" value="<%= i %>"/> <%= o %></label>
	<%});%>
	</div>
</script>
</body>
</html>
