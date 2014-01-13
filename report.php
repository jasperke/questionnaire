<?php
session_start();

if(!isset($_SESSION['admin'])||strcmp($_SESSION['admin'],'changgung')!=0){ // 未登入
	header('Location: ./login.php');
	exit;
}

// $pid
// $quest
require_once('koala.Utility.php');
require_once('common.Utility.php');
require_once('rpc.Utility2.php');
require_once('rpc/questionnaireUtility.php');
require_once('rpc/questionnaireMap.php');
define("CFG_FN", "/usr/local/koala/config.ini");

function getScore($answer,$quest,$key,$version){
	global $questionnaireMap;
	$ans=json_decode($answer);
	$idx=array_search($key,$questionnaireMap[$version][$quest]);
if($key=='H&N2')
	error_log("\nH&N2: ".$ans[$idx], 3,'/tmp/jasper.log');
	return ($idx===false)?'':$ans[$idx];
}


$group_id=2; // OwnerID
$dbid=2;
$out=array(array(0,''));
$record=array();
$record_fact_hn_x=array(); // HN.COM問卷, 需另外存FACT-HN-X資料來畫圖

$SiteLoginUID=kwut2_readini(CFG_FN,"KOALA","SiteUID");
$SiteLoginPWD=kwut2_readini(CFG_FN,"KOALA","SitePWD");
$db=kwcr2_mapdb('CyberSite',$SiteLoginUID,$SiteLoginPWD);
if($db!=0){
	if(strcmp($quest,'HN.COM')!=0){
		$s="select q.CreateTime,q.Answer,q.Weight,q.StaffID,u.Name,q.RandNum,q.Dose,q.Version,q.Questionnaire from MUST_Questionnaire q left outer join MUST_QuestionnaireUser u on q.No=u.No where q.OwnerID=? and q.Questionnaire=? and q.No=? order by q.CreateTime desc";
		$p=array($group_id,$quest,$pid);
	}else{
		$s="select q.CreateTime,q.Answer,q.Weight,q.StaffID,u.Name,q.RandNum,q.Dose,q.Version,q.Questionnaire from MUST_Questionnaire q left outer join MUST_QuestionnaireUser u on q.No=u.No where q.OwnerID=? and (q.Questionnaire=? or q.Questionnaire=?) and q.No=? order by q.CreateTime desc";
		$p=array($group_id,$quest,'FACT-HN-X',$pid); // HN.COM問卷, 須附帶抓FACT-HN-X資料來畫圖
	}
	//$rs=read_multi_record($db, $s, $p, array('skip_rows'=>0,'max_fetch_raw'=>5));
	$rs=read_multi_record($db, $s, $p);
	if($rs===false){
		$out[0]=array(900,"讀取資料失敗(1)！".kwcr2_geterrormsg($db, 1));
	}else if(!isset($rs)){
		$out[0]=array(901,"查無符合資料！".kwcr2_geterrormsg($db, 1));
	}else{
		$counter=0;
		$counter_fact_hn_x=(strcmp($quest,'HN.COM')==0)?0:5;
		foreach($rs as $r){
			// 根據answer即時算出各屬性量表的分數
			Calculator::reset();
			foreach(json_decode($r[1]) as $idx=>$ans){
				list($a,$sub_a)=explode(':',$ans);
				list($q_id,$sub_q)=explode(':',$questionnaireMap[$r[7]][$r[8]][$idx]);
				Calculator::inputAnswer($q_id,$a);
				// 目前子問題群尚無計分狀況, 暫不需處理
				// 新版題組也沒子問題群了
			}
			$detail=Calculator::outSum();

			$_d=array('date'=>substr($r[0],0,10),
					// column1 5張圖, 固定每種問卷都會有
					'fact'=>isset($detail['SUM'])?$detail['SUM']:'',
					'ge'=>isset($detail['GE'])?$detail['GE']:'',
					'gp'=>isset($detail['GP'])?$detail['GP']:'',
					'gf'=>isset($detail['GF'])?$detail['GF']:'',
					'gs'=>isset($detail['GS'])?$detail['GS']:'',

					// column2 (for FACT-B)
					'b'=>isset($detail['B'])?$detail['B']:'',
					'b2'=>getScore($r[1],$r[8],'B2',$r[7]),
					'b3'=>getScore($r[1],$r[8],'B3',$r[7]),
					'b6'=>getScore($r[1],$r[8],'B6',$r[7]),
					// column2 (for FACT-ECO)
					'ge6'=>getScore($r[1],$r[8],'GE6',$r[7]),
					'gf1'=>getScore($r[1],$r[8],'GF1',$r[7]),
					'gf7'=>getScore($r[1],$r[8],'GF7',$r[7]),
					'gp5'=>getScore($r[1],$r[8],'GP5',$r[7]),
					// column2 (for FACT-HN-X and HN.COM)
					'handn'=>isset($detail['H&N'])?$detail['H&N']:'',
					'np'=>isset($detail['NP'])?$detail['NP']:'',
					'handn2'=>getScore($r[1],$r[8],'H&N2',$r[7]),
					'handn7'=>getScore($r[1],$r[8],'H&N7',$r[7]),

					// column3 4張圖, 固定每種問卷都會有
					'pain'=>getScore($r[1],$r[8],'_SCORE_OF_PAIN',$r[7]),
					'hn2'=>getScore($r[1],$r[8],'_HN2',$r[7]),
					'hn1'=>getScore($r[1],$r[8],'_HN1',$r[7]),
					'hn3'=>getScore($r[1],$r[8],'_HN3',$r[7])
			);

			if(strcmp($r[8],$quest)==0){ // 當前查看的問卷資料
				if($counter<5){
					$record[]=$_d;
					$counter++;
				}
			}else{ // 看HN.COM, 另外找出的FACT-HN-X資料
				if($counter_fact_hn_x<5){
					$record_fact_hn_x[]=$_d;
					$counter_fact_hn_x++;
				}
			}
			if($counter>=5&&$counter_fact_hn_x>=5)
				break;
		}

		$patient_name=$rs[0][4];
		$doctor_id=$rs[0][3];
		$current_date=substr($rs[0][0],0,10);
		$current_record_id=global_unique_key_encode($rs[0][0],$rs[0][5]);
		$last_dose=strcmp($rs[1][6],'')==0?'{}':$rs[1][6];
		$last_dose_date=substr($rs[1][0],0,10);
		$current_dose=strcmp($rs[0][6],'')==0?'{}':$rs[0][6];

		$weight=array('','','');
		$weight[0]=number_format($rs[count($rs)-1][2], 1, '.', ',');
		if(isset($rs[1]))
			$weight[1]=number_format($rs[1][2], 1, '.', ',');
		$weight[2]=number_format($rs[0][2], 1, '.', ',');

		// 對照quizPool.js
		$E3=array('無收入','0-10,000元','10,001-30,000元','30,001-50,000元','50,001-100,000元','100,000-200,000元','大於200,000元');
		$money=$E3[getScore($rs[0][1],$quest,'E3',$rs[0][7])];

		$G1=array('全職','兼職','家庭主婦','失業','退休','殘障不能就業');
		$work=$G1[getScore($rs[0][1],$quest,'G1',$rs[0][7])];
	}

	// 醫師姓名
	$gukey=global_unique_key_decode($doctor_id);
	$s="select Name from MUST_Staff where OwnerID=? and CreateTime=? and RandNum=?";
	$p=array($group_id,$gukey[0],$gukey[1]);
	$r=read_one_record($db, $s, $p);
	if($r===false||!isset($r)){
		$out[0]=array(900,"讀取資料失敗(2)！".kwcr2_geterrormsg($db, 1));
	}else{
		$doctor_name=$r[0];
	}
}else{
	$out[0]=array(900,"資料庫連結失敗！");
}

?>
<!doctype html>
<html>
<head>
<meta charset=utf-8>
<meta name="viewport" content="user-scalable=yes, width=device-width" />
<title></title>
<style type="text/css">
<!--
@media print {
  .no-print {display:none !important;}
  input {border:none; border-bottom: 1px solid #000000; text-align:center;}
	body {margin:0mm 0mm 0mm 0mm;}
  table#main {
  	width:780px;
  }
  table#main td {font-size:14px;}
  table#innerTable td {border:1px solid #000000; font-size:10px;}
  table#innerTable th {border:1px solid #000000; text-align:center; font-size:10px;}
  div#fact_chart canvas {width:250px !important; height:210px !important;}
  canvas {width:250px; height:195px;}​
}
@media screen {
  body {margin:10px;}
  table#main {
  	width:100%;
  	border:1px solid #000000;
  	background-color:#ffffff;
  	padding:30px;
  }
  table#main td {font-size:15px;}
  table#innerTable {background-color:#000000;}
  table#innerTable td {background-color:#ffffff; font-size:14px; line-height:180%}
	table#innerTable tr.currentRow td{background-color:#FFDBCA !important;}
  table#innerTable th {background-color:#DBDBDB; text-align:center; font-size:14px;}
  div#fact_chart canvas {width:350px; height:294px !important;} /* 250x210 * 1.4 */
  canvas {width: 350px; height: 273px;}​ /* 250x195 * 1.4 */
}

}
canvas {border:1px solid #000000;}
input:checked + label {background-color:#FF9393;}
-->
</style>
<script src="js/jquery.min.js" ></script>
<script src="js/underscore-min.js" ></script>
<script src="js/main.min.js" ></script>
<script>
var	data=<? echo QUtillity::decodeUnicodeString(json_encode($out)); ?>,
	record=<? echo QUtillity::decodeUnicodeString(json_encode($record)); ?>,
	record_fact_hn=<? echo QUtillity::decodeUnicodeString(json_encode($record_fact_hn_x)); ?>,
	current_record_id='<? echo $current_record_id; ?>',
	last_dose=<? echo $last_dose; ?>,
	last_dose_date='<? echo $last_dose_date; ?>',
	current_dose=<? echo $current_dose; ?>,
	questionnaire_name='<? echo $quest; ?>';

function doSave(){
	var f=document.doseForm,
		result={};

	if(f.tr[0].checked){
		result.tr=1;
	}else if(f.tr[1].checked){
		result.tr=0;
	}
	if(f.trDate.value){
		result.trDate=f.trDate.value;
	}
	if(f.nr[0].checked){
		result.nr=1;
	}else if(f.nr[1].checked){
		result.nr=0;
	}
	if(f.nrDate.value){
		result.nrDate=f.nrDate.value;
	}
	if(f.mr[0].checked){
		result.mr=1;
	}else if(f.mr[1].checked){
		result.mr=0;
	}
	if(f.mrDate.value){
		result.mrDate=f.mrDate.value;
	}
	if(f.secondPrim[0].checked){
		result.secondPrim=1;
	}else if(f.secondPrim[1].checked){
		result.secondPrim=0;
	}
	if(f.secondPrimDate.value){
		result.secondPrimDate=f.secondPrimDate.value;
	}

	$('form :checked').each(function(){
		result[this.name]=this.value;
	})

	$.ajax({
		url:'rpc/updateDose.php',
		dataType:'json',
		type:'POST',
		data:{
			id:current_record_id,
			dose:result
		},
		error:function(){
			alert('error'); // TODO:
		},
		success:function(data){
			if(data[0][0]!=0){
				alert('錯誤！\n\n錯誤代碼：'+data[0][0]+'\n錯誤訊息：'+data[0][1]);
			}else{
				try{opener.getList();}
				catch(e){}
				alert('資料已儲存！');
			}
		}
	});
}
function xLabel(label){
	var t=label.split('-'),
		out=[];
	out.push(t[1]+'/'+t[2]);
	out.push(t[0]);
	return out;
}

$(function(){
	if(data[0][0]!==0){
		$('body').html('錯誤！<br><br>錯誤代碼：' + data[0][0] + '<br>錯誤訊息：' + data[0][1]);
		return;
	}

	$('.date').datePicker({
		weekName:['日','一','二','三','四','五','六'],
		monthName:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月']
	});

	var dataName=['fact','ge','gp','gf','gs', 'b','b2','b3','b6', 'ge6','gf1','gf7','gp5', 'handn','np','handn2','handn7', 'pain','hn2','hn1','hn3'],
		i,
		j;
	for(i=0; i<dataName.length; i++){
		window[dataName[i]+'Data']=[];
	}
	for(j=record.length-1; j>=0; j--){
		for(i=0; i<dataName.length; i++){
			window[dataName[i]+'Data'].push({label:record[j].date,value:record[j][dataName[i]]});
		}
	}
	if(questionnaire_name=='HN.COM'){ // HN.COM column1和2 各圖資料須用FACT-HN-X的
		for(i=0; i<dataName.length; i++){
			if(i<=4||(i>=13&&i<=16)){
				window[dataName[i]+'Data']=[];
			}
		}
		for(j=record_fact_hn.length-1; j>=0; j--){
			for(i=0; i<dataName.length; i++){
				if(i<=4||(i>=13&&i<=16)){
					window[dataName[i]+'Data'].push({label:record_fact_hn[j].date,value:record_fact_hn[j][dataName[i]]});
				}
			}
		}
	}

	new CyberChart('fact_chart', factData, {xTitle:'FACT總分', yTitle:'總分', type:'line', height:840, yScale:[30,40,50,60,70,80,90,100,110], xLabel:xLabel});
	new CyberChart('ge_chart', geData, {xTitle:'情緒', yTitle:'分數', height:780, xLabel:xLabel});
	new CyberChart('gp_chart', gpData, {xTitle:'生理', yTitle:'分數', height:780, xLabel:xLabel});
	new CyberChart('gf_chart', gfData, {xTitle:'功能', yTitle:'分數', height:780, xLabel:xLabel});
	new CyberChart('gs_chart', gsData, {xTitle:'社會/家庭', yTitle:'分數', height:780, xLabel:xLabel});

	if($('#b_chart').size())
		new CyberChart('b_chart', bData, {xTitle:'Breast附加總分', yTitle:'分數', yScale:[0,10,20,30,40], height:780, xLabel:xLabel});
	if($('#b2_chart').size())
		new CyberChart('b2_chart', b2Data, {xTitle:'我在意自己的衣服穿著', yTitle:'分數', yScale:[0,1,2,3,4], height:780, xLabel:xLabel});
	if($('#b3_chart').size())
		new CyberChart('b3_chart', b3Data, {xTitle:'我有一側或兩側的手臂腫脹或疼痛', yTitle:'分數', yScale:[0,1,2,3,4], height:780, xLabel:xLabel});
	if($('#b6_chart').size())
		new CyberChart('b6_chart', b6Data, {xTitle:'我擔心家人也會有得跟我同樣疾病的風險', yTitle:'分數', yScale:[0,1,2,3,4], height:780, xLabel:xLabel});

	if($('#ge6_chart').size())
		new CyberChart('ge6_chart', ge6Data, {xTitle:'我擔心我的狀況會惡化', yTitle:'分數', yScale:[0,1,2,3,4], height:780, xLabel:xLabel});
	if($('#gf1_chart').size())
		new CyberChart('gf1_chart', gf1Data, {xTitle:'我能夠工作(包括在家的工作)', yTitle:'分數', yScale:[0,1,2,3,4], height:780, xLabel:xLabel});
	if($('#gf7_chart').size())
		new CyberChart('gf7_chart', gf7Data, {xTitle:'我滿足我現在的生活品質', yTitle:'分數', yScale:[0,1,2,3,4], height:780, xLabel:xLabel});
	if($('#gp5_chart').size())
		new CyberChart('gp5_chart', gp5Data, {xTitle:'我對治療的副作用感到困擾', yTitle:'分數', yScale:[0,1,2,3,4], height:780, xLabel:xLabel});

	if($('#handn_chart').size())
		new CyberChart('handn_chart', handnData, {xTitle:'H&N附加總分', yTitle:'總分', yScale:[0,10,20,30,40], height:780, xLabel:xLabel});
	if($('#np_chart').size())
		new CyberChart('np_chart', npData, {xTitle:'NP附加總分', yTitle:'總分', yScale:[0,10,20,30,40], height:780, xLabel:xLabel});
	if($('#handn2_chart').size())
		new CyberChart('handn2_chart', handn2Data, {xTitle:'我覺得口乾', yTitle:'總分', yScale:[0,1,2,3,4], height:780, xLabel:xLabel});
	if($('#handn7_chart').size())
		new CyberChart('handn7_chart', handn7Data, {xTitle:'我能吞嚥自如', yTitle:'總分', yScale:[0,1,2,3,4], height:780, xLabel:xLabel});

	new CyberChart('pain_chart', painData, {xTitle:'感到疼痛程度', yTitle:'分數', yScale:[0,2,4,6,8,10], height:780, xLabel:xLabel});
	new CyberChart('hn2_chart', hn2Data, {xTitle:'失眠程度', yTitle:'分數', yScale:[0,1,2,3], height:780, xLabel:xLabel});
	new CyberChart('hn1_chart', hn1Data, {xTitle:'疲勞', yTitle:'分數', yScale:[0,1,2,3], height:780, xLabel:xLabel});
	new CyberChart('hn3_chart', hn3Data, {xTitle:'我有額外補充營養嗎', yTitle:'分數', yScale:[0,1,2,3], height:780, xLabel:xLabel});


	if(record.length>=2&&record[0].pain!==''&&record[1].pain!==''){
		var diff=record[0].pain-record[1].pain;
		if(diff<0){
			diff='-'+Math.abs(diff);
		}else if(diff>0){
			diff='+'+diff;
		}
		$('#painDiff').html('<br>感到疼痛程度差異：'+diff);
	}

	// Tr,Nr,Mr...
	var fourItem=['tr','nr','mr','secondPrim'],
		fourDate=['trDate','nrDate','mrDate','secondPrimDate'],
		i;
	for(i=0; i<4; i++){
		if(current_dose[fourItem[i]]!==undefined){
			document.doseForm[fourItem[i]][current_dose[fourItem[i]]==1?0:1].checked=true;
		}
	}
	for(i=0; i<4; i++){
		if(current_dose[fourDate[i]]!==undefined){
			document.doseForm[fourDate[i]].value=current_dose[fourDate[i]];
		}
	}
	$('#innerTable').on('mouseover mouseout','td',function(event){
			if(event.type=='mouseover'){
				$(this).parent('tr').addClass('currentRow');
			}else{
				$(this).parent('tr').removeClass('currentRow');
			}
	}).on('click','td',function(event){
		$(this).find(':input').get(0).checked=true;
	});
	// 勾選下方dose表格
	var dose_items=["lymphedema","dermatitis","fibrosis","telangiectasia","mucosistis","stricture","cough","laryngeal_edema","osteonecrosis","ischemia","neuropathy","hypothyroidism"];
	for(var i=0; i<dose_items.length; i++){
		if(current_dose[dose_items[i]]!==undefined){
			//$('[name='+dose_items[i]+']').each(function(){
			$(document.doseForm[dose_items[i]]).each(function(){
				if(this.value==current_dose[dose_items[i]]){
					this.checked=true;
					return;
				}
			});
		}else{ // 預設勾0
			document.doseForm[dose_items[i]][0].checked=true;
		}
	}
	// 顯示右上last dose record
	var here=$('#last_dose_list'),
		gotLast_dose=false;
	for(var i=0; i<dose_items.length; i++){
		if(last_dose[dose_items[i]]!==undefined){
			here.append(dose_items[i]+'- '+last_dose[dose_items[i]]+'<br>');
			gotLast_dose=true;
		}
	}
	if(gotLast_dose){
		here.prepend('前次紀錄日期：'+last_dose_date+'<br>--------------------------------------------<br>');
	}
});

// 中間column的4個圖, 依不同問卷別而不同
function column2chart(idx){
	var q_db={
		'FACT-B':['b','b2','b3','b6'],
		'FACT-ECO':['ge6','gf1','gf7','gp5'],
		'FACT-HN-X':['handn','np','handn2','handn7'],
		'HN.COM':['handn','np','handn2','handn7']
	};
	return '<div id="'+q_db[questionnaire_name][idx]+'_chart" style="text-align:center;"></div>';
}
</script>
</head>

<body bgcolor="#EEEEEE">
<form name="doseForm" style="margin:0px;">
<table id="main" width="100%" border="0" align="center" cellspacing="5" cellpadding="0">
	<tr><td colspan="2">病歷號：<? echo $pid,'（',$patient_name,'）'; ?></td>
		<td colspan="2">體重：<? echo implode('&nbsp;/&nbsp;',$weight); ?></td>
		<td colspan="2">受訪日期：<? echo $current_date; ?></td></tr>
	<tr><td colspan="2">問卷別：<? echo $quest; ?></td>
		<td colspan="2">看診醫師：<? echo $doctor_name; ?>醫師</td>
		<td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="6">&nbsp;</td></tr>
	<tr><td colspan="2"><div id="fact_chart" style="text-align:center;"></div></td>
		<td colspan="2" style="vertical-align:top;">Pain分析值≧±2：<div id="painDiff" style="font-size:12px;"></div></td>
		<td colspan="2" style="vertical-align:top;">Last dose record：<br>
			<div id="last_dose_list" style="font-size:12px;"></div></td></tr>
	<tr><td colspan="2"><div id="ge_chart" style="text-align:center;"></div></td>
		<td colspan="2"><script>document.write(column2chart(0));</script></td>
		<td colspan="2"><div id="pain_chart" style="text-align:center;"></div></td></tr>
	<tr><td colspan="2"><div id="gp_chart" style="text-align:center;"></div></td>
		<td colspan="2"><script>document.write(column2chart(1));</script></td>
		<td colspan="2"><div id="hn2_chart" style="text-align:center;"></div></td></tr>
	<tr><td colspan="2"><div id="gf_chart" style="text-align:center;"></div></div></td>
		<td colspan="2"><script>document.write(column2chart(2));</script></td>
		<td colspan="2"><div id="hn1_chart" style="text-align:center;"></div></td></tr>
	<tr><td colspan="2">
		<div id="gs_chart" style="text-align:center;"></div></td>
		<td colspan="2"><script>document.write(column2chart(3));</script></td>
		<td colspan="2"><div id="hn3_chart" style="text-align:center;"></div></td></tr>
	<tr><td colspan="4">最近三個月可支配自由使用的錢約有 <? echo $money; ?></td>
		<td colspan="2">工作僱用狀態：<? echo $work; ?></td></tr>
	<tr><td colspan="6">
		<br>
		<table border="0" width="100%" cellspacing="0" cellpadding="2">
			<tr><td align="right">Tr</td>
					<td align="center"><small><label><input type="radio" name="tr" value="1">Y</label><br><label><input type="radio" name="tr" value="0">N</label></small></td>
					<td><input class="date" type="text" name="trDate" style="width:100px;"></td>
					<td align="right">Nr</td>
					<td align="center"><small><label><input type="radio" name="nr" value="1">Y</label><br><label><input type="radio" name="nr" value="0">N</label></small></td>
					<td><input class="date" type="text" name="nrDate" style="width:100px;"></td>
					<td align="right">Mr</td>
					<td align="center" class="smaller"><small><label><input type="radio" name="mr" value="1">Y</label><br><label><input type="radio" name="mr" value="0">N</label></small></td>
					<td><input class="date" type="text" name="mrDate" style="width:100px;"></td>
					<td align="right">2nd Prim</td>
					<td align="center" class="smaller"><small><label><input type="radio" name="secondPrim" value="1">Y</label><br><label><input type="radio" name="secondPrim" value="0">N</label></small></td>
					<td><input class="date" type="text" name="secondPrimDate" style="width:100px;"></td></tr></td></tr>
		</table>
	<tr><td colspan="6">
		<table id="innerTable" border="0" width="100%" cellspacing="1" cellpadding="1">
			<tr><th>&nbsp;</th>
				<th>1</th>
				<th>2</th>
				<th>3</th>
				<th>4</th>
				<th>5</th></tr>
			<tr><td>lymphedema</td>
				<td><input type="radio" id="lymphedema0" name="lymphedema" value="0"><label for="lymphedema0">0</label></td>
				<td><input type="radio" id="lymphedema1" name="lymphedema" value="1"><label for="lymphedema1">localized / disability(-)</label></td>
				<td><input type="radio" id="lymphedema2" name="lymphedema" value="2"><label for="lymphedema2">localized / disability(+)</label></td>
				<td><input type="radio" id="lymphedema3" name="lymphedema" value="3"><label for="lymphedema3">generalized / disability(+)</label></td>
				<td><input type="radio" id="lymphedema4" name="lymphedema" value="4"><label for="lymphedema4">ulceration / cerebral edema / tube</label></td>
				</tr>
			<tr><td>dermatitis</td>
				<td><input type="radio" id="dermatitis0" name="dermatitis" value="0"><label for="dermatitis0">0</label></td>
				<td><input type="radio" id="dermatitis1" name="dermatitis" value="1"><label for="dermatitis1">faint erythema dry</label></td>
				<td><input type="radio" id="dermatitis2" name="dermatitis" value="2"><label for="dermatitis2">brisk erythema / patchy moist</label></td>
				<td><input type="radio" id="dermatitis3" name="dermatitis" value="3"><label for="dermatitis3">confluent moist / touching bleeding</label></td>
				<td><input type="radio" id="dermatitis4" name="dermatitis" value="4"><label for="dermatitis4">ulceration / spontaneous bleeding</label></td></tr>
			<tr><td>fibrosis</td>
				<td><input type="radio" id="fibrosis0" name="fibrosis" value="0"><label for="fibrosis0">0</label></td>
				<td><input type="radio" id="fibrosis1" name="fibrosis" value="1"><label for="fibrosis1">increase density</label></td>
				<td><input type="radio" id="fibrosis2" name="fibrosis" value="2"><label for="fibrosis2">ADL(-) / firm / tightness</label></td>
				<td><input type="radio" id="fibrosis3" name="fibrosis" value="3"><label for="fibrosis3">ADL(+) / fixation or retraction</label></td>
				<td>&nbsp;</td></tr>
			<tr><td>telangiectasia</td>
				<td><input type="radio" id="telangiectasia0" name="telangiectasia" value="0"><label for="telangiectasia0">0</label></td>
				<td><input type="radio" id="telangiectasia1" name="telangiectasia" value="1"><label for="telangiectasia1">few</label></td>
				<td><input type="radio" id="telangiectasia2" name="telangiectasia" value="2"><label for="telangiectasia2">moderate</label></td>
				<td><input type="radio" id="telangiectasia3" name="telangiectasia" value="3"><label for="telangiectasia3">many / confluence</label></td>
				<td>&nbsp;</td></tr>
			<tr><td>mucosistis(E)</td>
				<td><input type="radio" id="mucosistis0" name="mucosistis" value="0"><label for="mucosistis0">0</label></td>
				<td><input type="radio" id="mucosistis1" name="mucosistis" value="1"><label for="mucosistis1">erythema</label></td>
				<td><input type="radio" id="mucosistis2" name="mucosistis" value="2"><label for="mucosistis2">patchy</label></td>
				<td><input type="radio" id="mucosistis3" name="mucosistis" value="3"><label for="mucosistis3">confluence / touch bleeding</label></td>
				<td><input type="radio" id="mucosistis4" name="mucosistis" value="4"><label for="mucosistis4">necrosis spontaneous bleeding</label></td></tr>
			<tr><td>stricture</td>
				<td><input type="radio" id="stricture0" name="stricture" value="0"><label for="stricture0">0</label></td>
				<td><input type="radio" id="stricture1" name="stricture" value="1"><label for="stricture1">asymptomatic</label></td>
				<td><input type="radio" id="stricture2" name="stricture" value="2"><label for="stricture2">altered dietary habits</label></td>
				<td><input type="radio" id="stricture3" name="stricture" value="3"><label for="stricture3">tube feeding</label></td>
				<td><input type="radio" id="stricture4" name="stricture" value="4"><label for="stricture4">op indicated / life threatening</label></td></tr>
			<tr><td>cough</td>
				<td><input type="radio" id="cough0" name="cough" value="0"><label for="cough0">0</label></td>
				<td><input type="radio" id="cough1" name="cough" value="1"><label for="cough1">codeine(-)</label></td>
				<td><input type="radio" id="cough2" name="cough" value="2"><label for="cough2">codeine(+)</label></td>
				<td><input type="radio" id="cough3" name="cough" value="3"><label for="cough3">ADL(+) / insomnia</label></td>
				<td>&nbsp;</td></tr>
			<tr><td>laryngeal edema</td>
				<td><input type="radio" id="laryngeal_edema0" name="laryngeal_edema" value="0"><label for="laryngeal_edema0">0</label></td>
				<td><input type="radio" id="laryngeal_edema1" name="laryngeal_edema" value="1"><label for="laryngeal_edema1">asymptomatic(E)</label></td>
				<td><input type="radio" id="laryngeal_edema2" name="laryngeal_edema" value="2"><label for="laryngeal_edema2">sorethroat / hoarseness</label></td>
				<td><input type="radio" id="laryngeal_edema3" name="laryngeal_edema" value="3"><label for="laryngeal_edema3">ADL(+) /stridor</label></td>
				<td><input type="radio" id="laryngeal_edema4" name="laryngeal_edema" value="4"><label for="laryngeal_edema4">life threatening / tracheostomy</label></td></tr>
			<tr><td>osteonecrosis</td>
				<td><input type="radio" id="osteonecrosis0" name="osteonecrosis" value="0"><label for="osteonecrosis0">0</label></td>
				<td><input type="radio" id="osteonecrosis1" name="osteonecrosis" value="1"><label for="osteonecrosis1">asymptomatic(E)</label></td>
				<td><input type="radio" id="osteonecrosis2" name="osteonecrosis" value="2"><label for="osteonecrosis2">ADL(-) / Symptomatic</label></td>
				<td><input type="radio" id="osteonecrosis3" name="osteonecrosis" value="3"><label for="osteonecrosis3">ADL(+) / HBO / OP</label></td>
				<td><input type="radio" id="osteonecrosis4" name="osteonecrosis" value="4"><label for="osteonecrosis4">disabling</label></td></tr>
			<tr><td>ischemia</td>
				<td><input type="radio" id="ischemia0" name="ischemia" value="0"><label for="ischemia0">0</label></td>
				<td>&nbsp;</td>
				<td><input type="radio" id="ischemia2" name="ischemia" value="2"><label for="ischemia2">asymptomatic(E)</label></td>
				<td><input type="radio" id="ischemia3" name="ischemia" value="3"><label for="ischemia3">TIA &lt; 24 hrs</label></td>
				<td><input type="radio" id="ischemia4" name="ischemia" value="4"><label for="ischemia4">stroke(+)</label></td>
			</tr>
			<tr><td>neuropathy</td>
				<td><input type="radio" id="neuropathy0" name="neuropathy" value="0"><label for="neuropathy0">0</label></td>
				<td><input type="radio" id="neuropathy1" name="neuropathy" value="1"><label for="neuropathy1">asymptomatic(E)</label></td>
				<td><input type="radio" id="neuropathy2" name="neuropathy" value="2"><label for="neuropathy2">ADL(-) / symptomatic</label></td>
				<td><input type="radio" id="neuropathy3" name="neuropathy" value="3"><label for="neuropathy3">ADL(+)</label></td>
				<td><input type="radio" id="neuropathy4" name="neuropathy" value="4"><label for="neuropathy4">life threatening / disabling</label></td></tr>
			<tr><td>hypothyroidism</td>
				<td><input type="radio" id="hypothyroidism0" name="hypothyroidism" value="0"><label for="hypothyroidism0">0</label></td>
				<td><input type="radio" id="hypothyroidism1" name="hypothyroidism" value="1"><label for="hypothyroidism1">asymptomatic(E)</label></td>
				<td><input type="radio" id="hypothyroidism2" name="hypothyroidism" value="2"><label for="hypothyroidism2">ADL(-) / replacement</label></td>
				<td>&nbsp;</td>
				<td><input type="radio" id="hypothyroidism4" name="hypothyroidism" value="4"><label for="hypothyroidism4">life threatening / coma</label></td></tr>
		</table>
		<div class="no-print" style="padding-top:16px; text-align:center;"><input type="button" name="saveButton" value="儲存" onclick="doSave();"></div>
		</td></tr>
</table>
</form>
</body>
</html>
