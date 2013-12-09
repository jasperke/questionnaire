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

function getScore($answer,$quest,$key){
	global $questionnaireMap;
	$ans=json_decode($answer);
	$idx=array_search($key,$questionnaireMap[$quest]);
	return ($idx===false)?'':$ans[$idx];
}


$group_id=2; // OwnerID
$dbid=2;
$out=array(array(0,''));
$record=array();

$SiteLoginUID=kwut2_readini(CFG_FN,"KOALA","SiteUID");
$SiteLoginPWD=kwut2_readini(CFG_FN,"KOALA","SitePWD");
$db=kwcr2_mapdb('CyberSite',$SiteLoginUID,$SiteLoginPWD);
if($db!=0){
	$s="select q.CreateTime,q.Answer,q.Weight,q.StaffID,u.Name,q.RandNum,q.Dose from MUST_Questionnaire q left outer join MUST_QuestionnaireUser u on q.No=u.No where q.OwnerID=? and q.Questionnaire=? and q.No=? order by q.CreateTime desc";
	$p=array($group_id,$quest,$pid);
	//$rs=read_multi_record($db, $s, $p, array('skip_rows'=>0,'max_fetch_raw'=>5));
	$rs=read_multi_record($db, $s, $p);
	if($rs===false){
		$out[0]=array(900,"讀取資料失敗(1)！".kwcr2_geterrormsg($db, 1));
	}else if(!isset($rs)){
		$out[0]=array(901,"查無符合資料！".kwcr2_geterrormsg($db, 1));
	}else{
		$counter=0;
		foreach($rs as $r){
			if($counter>=5)
				break;

			// 根據answer即時算出各屬性量表的分數
			Calculator::reset();
			foreach(json_decode($r[1]) as $idx=>$ans){
				list($a,$sub_a)=explode(':',$ans);
				list($q_id,$sub_q)=explode(':',$questionnaireMap[$quest][$idx]);
				Calculator::inputAnswer($q_id,$a);
				// 目前子問題群尚無計分狀況, 暫不需處理
			}
			$detail=Calculator::outSum();
			$record[]=array('date'=>substr($r[0],0,10),
//					'answer'=>$r[1],
					'gp'=>isset($detail['GP'])?$detail['GP']:'',
					'gs'=>isset($detail['GS'])?$detail['GS']:'',
					'ge'=>isset($detail['GE'])?$detail['GE']:'',
					'gf'=>isset($detail['GF'])?$detail['GF']:'',
					'h&n'=>isset($detail['H&N'])?$detail['H&N']:'',
					'pain'=>getScore($r[1],$quest,'_SCORE_OF_PAIN'),
					'hn2'=>getScore($r[1],$quest,'_HN2'),
					'hn1'=>getScore($r[1],$quest,'_HN1'));
			$counter++;
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
		$money=$E3[getScore($rs[0][1],$quest,'E3')];

		$G1=array('全職','兼職','家庭主婦','失業','退休','殘障不能就業');
		$work=$G1[getScore($rs[0][1],$quest,'G1')];
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
  div#hn1_chart canvas {width:250px !important; height:160px !important;}
  canvas {width:250px; height:210px;}​
}
@media screen {
  body {margin:10px;}
  table#main {
  	width:1080px;
  	border:1px solid #000000;
  	background-color:#ffffff;
  	padding:30px;
  }
  table#main td {font-size:15px;}
  table#innerTable {background-color:#000000;}
  table#innerTable td {background-color:#ffffff; font-size:14px;}
  table#innerTable th {background-color:#DBDBDB; text-align:center; font-size:14px;}
  div#hn1_chart canvas{width:350px; height:224px !important;} /* 250x160 * 1.4 */
  canvas {width: 350px; height: 294px;}​ /* 250x210 * 1.4 */
}
canvas {border:1px solid #000000;}

-->
</style>
<script src="js/jquery.min.js" ></script>
<script src="js/underscore-min.js" ></script>
<script src="js/main.min.js" ></script>
<!-- <script src="js/CyberChart.js" ></script> -->
<script>
var	data=<? echo QUtillity::decodeUnicodeString(json_encode($out)); ?>,
	record=<? echo QUtillity::decodeUnicodeString(json_encode($record)); ?>,
	current_record_id='<? echo $current_record_id; ?>',
	last_dose=<? echo $last_dose; ?>,
	last_dose_date='<? echo $last_dose_date; ?>',
	current_dose=<? echo $current_dose; ?>;

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
				alert('資料已儲存！');
			}
		}
	});
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

	var handnData=[],
		geData=[],
		gfData=[],
		painData=[],
		gpData=[],
		gsData=[],
		hn2Data=[],
		hn1Data=[],
		i;
	for(i=record.length-1; i>=0; i--){
			handnData.push({label:record[i].date,value:record[i]['h&n']});
			geData.push({label:record[i].date,value:record[i].ge});
			gfData.push({label:record[i].date,value:record[i].gf});
			painData.push({label:record[i].date,value:record[i].pain});
			gpData.push({label:record[i].date,value:record[i].gp});
			gsData.push({label:record[i].date,value:record[i].gs});
			hn2Data.push({label:record[i].date,value:record[i].hn2});
			hn1Data.push({label:record[i].date,value:record[i].hn1});
	}
	new CyberChart('handn_chart', handnData, {xTitle:'H&N', yTitle:'總分', type:'line'});
	new CyberChart('ge_chart', geData, {xTitle:'情緒', yTitle:'分數'});
	new CyberChart('gf_chart', gfData, {xTitle:'功能', yTitle:'分數'});
	new CyberChart('pain_chart', painData, {xTitle:'感到疼痛程度', yTitle:'分數', yScale:[0,2,4,6,8,10]});
	new CyberChart('gp_chart', gpData, {xTitle:'生理', yTitle:'分數'});
	new CyberChart('gs_chart', gsData, {xTitle:'社會/家庭', yTitle:'分數'});
	new CyberChart('hn2_chart', hn2Data, {xTitle:'失眠程度', yTitle:'分數', yScale:[0,1,2,3]});
	new CyberChart('hn1_chart', hn1Data, {xTitle:'疲勞', yTitle:'分數', yScale:[0,1,2,3], width:1000, height:640});

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
	// 勾選下方dose表格
	var dose_items=["lymphedema","dermatitis","fibrosis","telangiectasia","mucosistis","stricture","cough","laryngeal_edema","osteonecrosis","ischemia","neuropathy","hypothyroidism"];
	for(var i=0; i<dose_items.length; i++){
		if(current_dose[dose_items[i]]!==undefined){
			$('[name='+dose_items[i]+']').each(function(){
				if(this.value==current_dose[dose_items[i]]){
					this.checked=true;
					return;
				}
			});
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
})
</script>
</head>

<body bgcolor="#EEEEEE">
<form name="doseForm" style="margin:0px;">
<table id="main" border="0" align="center" cellspacing="5" cellpadding="0">
	<tr><td colspan="2">病歷號：<? echo $pid,'（',$patient_name,'）'; ?></td>
		<td colspan="2">體重：<? echo implode('&nbsp;/&nbsp;',$weight); ?></td>
		<td colspan="2">受訪日期：<? echo $current_date; ?></td></tr>
	<tr><td colspan="2">問卷別：<? echo $quest; ?></td>
		<td colspan="2">看診醫師：<? echo $doctor_name; ?>醫師</td>
		<td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="6">&nbsp;</td></tr>
	<tr><td colspan="2"><div id="handn_chart"></div><!-- <img border="1" src="space.gif" width="210" height="200"><br>H&N圖 --></td>
		<td colspan="2" style="vertical-align:top;">Pain分析值≧±2：<div id="painDiff" style="font-size:12px;"></div>
			<div id="hn1_chart" style="margin-top:10px;"></div></td>
		<td colspan="2" style="vertical-align:top;">Last dose record：<br>
			<div id="last_dose_list" style="font-size:12px;"></div></td></tr>
	<tr><td colspan="2"><div id="ge_chart"></div></td>
		<td colspan="2"><div id="gf_chart"></div></td>
		<td colspan="2"><div id="pain_chart"></div></td></tr>
	<tr><td colspan="2"><div id="gp_chart"></div></td>
		<td colspan="2"><div id="gs_chart"></div></td>
		<td colspan="2"><div id="hn2_chart"></div></td></tr>
	<tr><td colspan="3">最近三個月可支配自由使用的錢約有 <? echo $money; ?></td>
		<td colspan="3">工作僱用狀態：<? echo $work; ?></td></tr>
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
				<td><label><input type="radio" name="lymphedema" value="0">0</label></td>
				<td><label><input type="radio" name="lymphedema" value="1">localized / disability(-)</label></td>
				<td><label><input type="radio" name="lymphedema" value="2">localized / disability(+)</label></td>
				<td><label><input type="radio" name="lymphedema" value="3">generalized / disability(+)</label></td>
				<td><label><input type="radio" name="lymphedema" value="4">ulceration / cerebral edema / tube</label></td>
				</tr>
			<tr><td>dermatitis</td>
				<td><label><input type="radio" name="dermatitis" value="0">0</label></td>
				<td><label><input type="radio" name="dermatitis" value="1">faint erythema dry</label></td>
				<td><label><input type="radio" name="dermatitis" value="2">brisk erythema / patchy moist</label></td>
				<td><label><input type="radio" name="dermatitis" value="3">confluent moist / touching bleeding</label></td>
				<td><label><input type="radio" name="dermatitis" value="4">ulceration / spontaneous bleeding</label></td></tr>
			<tr><td>fibrosis</td>
				<td><label><input type="radio" name="fibrosis" value="0">0</label></td>
				<td><label><input type="radio" name="fibrosis" value="1">increase density</label></td>
				<td><label><input type="radio" name="fibrosis" value="2">ADL(-) / firm / tightness</label></td>
				<td><label><input type="radio" name="fibrosis" value="3">ADL(+) / fixation or retraction</label></td>
				<td>&nbsp;</td></tr>
			<tr><td>telangiectasia</td>
				<td><label><input type="radio" name="telangiectasia" value="0">0</label></td>
				<td><label><input type="radio" name="telangiectasia" value="1">few</label></td>
				<td><label><input type="radio" name="telangiectasia" value="2">moderate</label></td>
				<td><label><input type="radio" name="telangiectasia" value="3">many / confluence</label></td>
				<td>&nbsp;</td></tr>
			<tr><td>mucosistis(E)</td>
				<td><label><input type="radio" name="mucosistis" value="0">0</label></td>
				<td><label><input type="radio" name="mucosistis" value="1">erythema</label></td>
				<td><label><input type="radio" name="mucosistis" value="2">patchy</label></td>
				<td><label><input type="radio" name="mucosistis" value="3">confluence / touch bleeding</label></td>
				<td><label><input type="radio" name="mucosistis" value="4">necrosis spontaneous bleeding</label></td></tr>
			<tr><td>stricture</td>
				<td><label><input type="radio" name="stricture" value="0">0</label></td>
				<td><label><input type="radio" name="stricture" value="1">asymptomatic</label></td>
				<td><label><input type="radio" name="stricture" value="2">altered dietary habits</label></td>
				<td><label><input type="radio" name="stricture" value="3">tube feeding</label></td>
				<td><label><input type="radio" name="stricture" value="4">op indicated / life threatening</label></td></tr>
			<tr><td>cough</td>
				<td><label><input type="radio" name="cough" value="0">0</label></td>
				<td><label><input type="radio" name="cough" value="1">codeine(-)</label></td>
				<td><label><input type="radio" name="cough" value="2">codeine(+)</label></td>
				<td><label><input type="radio" name="cough" value="3">ADL(+) / insomnia</label></td>
				<td>&nbsp;</td></tr>
			<tr><td>laryngeal edema</td>
				<td><label><input type="radio" name="laryngeal_edema" value="0">0</label></td>
				<td><label><input type="radio" name="laryngeal_edema" value="1">asymptomatic(E)</label></td>
				<td><label><input type="radio" name="laryngeal_edema" value="2">sorethroat / hoarseness</label></td>
				<td><label><input type="radio" name="laryngeal_edema" value="3">ADL(+) /stridor</label></td>
				<td><label><input type="radio" name="laryngeal_edema" value="4">life threatening / tracheostomy</label></td></tr>
			<tr><td>osteonecrosis</td>
				<td><label><input type="radio" name="osteonecrosis" value="0">0</label></td>
				<td><label><input type="radio" name="osteonecrosis" value="1">asymptomatic(E)</label></td>
				<td><label><input type="radio" name="osteonecrosis" value="2">ADL(-) / Symptomatic</label></td>
				<td><label><input type="radio" name="osteonecrosis" value="3">ADL(+) / HBO / OP</label></td>
				<td><label><input type="radio" name="osteonecrosis" value="4">disabling</label></td></tr>
			<tr><td>ischemia</td>
				<td><label><input type="radio" name="ischemia" value="0">0</label></td>
				<td>&nbsp;</td>
				<td><label><input type="radio" name="ischemia" value="2">asymptomatic(E)</label></td>
				<td><label><input type="radio" name="ischemia" value="3">TIA &lt; 24 hrs</label></td>
				<td><label><input type="radio" name="ischemia" value="4">stroke(+)</label></td>
			</tr>
			<tr><td>neuropathy</td>
				<td><label><input type="radio" name="neuropathy" value="0">0</label></td>
				<td><label><input type="radio" name="neuropathy" value="1">asymptomatic(E)</label></td>
				<td><label><input type="radio" name="neuropathy" value="2">ADL(-) / symptomatic</label></td>
				<td><label><input type="radio" name="neuropathy" value="3">ADL(+)</label></td>
				<td><label><input type="radio" name="neuropathy" value="4">life threatening / disabling</label></td></tr>
			<tr><td>hypothyroidism</td>
				<td><label><input type="radio" name="hypothyroidism" value="0">0</label></td>
				<td><label><input type="radio" name="hypothyroidism" value="1">asymptomatic(E)</label></td>
				<td><label><input type="radio" name="hypothyroidism" value="2">ADL(-) / replacement</label></td>
				<td>&nbsp;</td>
				<td><label><input type="radio" name="hypothyroidism" value="4">life threatening / coma</label></td></tr>
		</table>
		<div class="no-print" style="padding-top:16px; text-align:center;"><input type="button" name="saveButton" value="儲存" onclick="doSave();"></div>
		</td></tr>
</table>
</form>
</body>
</html>
