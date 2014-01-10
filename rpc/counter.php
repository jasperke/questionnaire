<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once('koala.Utility.php');
require_once('common.Utility.php');
require_once('rpc.Utility2.php');
require_once('questionnaireMap.php');
require_once('questionnaireUtility.php');
define("CFG_FN", "/usr/local/koala/config.ini");

if(!isset($_SESSION['admin'])||strcmp($_SESSION['admin'],'changgung')!=0){ // 未登入
	$out=array(array(902,"無權限使用！"));
	echo QUtillity::decodeUnicodeString(json_encode($out));
	exit;
}

/*
$questionnaire="HN.COM";
$p_id="898989";
$p_name="hjlkjl";
$p_weight="56";
$answer=["0","1","1","2","1","3","1","0","2","1","0","1","2","1","2","3","0","1","2","1","0","1"];
$answer說明: ex.["0","2","1:0,1,2",...] 其中"1:0,1,2" 表示主問題選1, 冒號後表示 子問題群依序選0,1,2
*/

foreach($answer as $idx=>$ans){
	list($a,$sub_a)=explode(':',$ans);
	list($q_id,$sub_q)=explode(':',$questionnaireMap[CURRENT_VERSION][$questionnaire][$idx]);
	Calculator::inputAnswer($q_id,$a);
	/* 目前子問題群尚無計分狀況, 暫不需處理
	if(isset($sub_a)&&str_cmp($sub_a,'')!=''){
		$sub_a=explode(',',$sub_a);
		if(count($sub_a)){
			foreach($sub_a as $a){

			}
		}
	}*/
}
$detail=Calculator::outSum();
$score=$detail['SUM'];
$need2UpdateWeight=true;

$out=array(array(0,''));
$group_id=2; // OwnerID
$dbid=2;


$SiteLoginUID=kwut2_readini(CFG_FN,"KOALA","SiteUID");
$SiteLoginPWD=kwut2_readini(CFG_FN,"KOALA","SitePWD");
$db=kwcr2_mapdb('CyberSite',$SiteLoginUID,$SiteLoginPWD);
if($db!=0){
	if(!kwcr2_starttransaction($db)){
		$out[0]=array(900,"failed to start transaction!(".kwcr2_geterrormsg($db,1).")");
		echo QUtillity::decodeUnicodeString(json_encode($out));
		exit;
	}

	$s="select count(*) from MUST_QuestionnaireUser where No=?";
	$r=read_one_record($db, $s, array($p_id));
	if($r===false||!isset($r)){
		$out[0]=array(900,"資料儲存失敗(0)！(".kwcr2_geterrormsg($db,1).")");
		echo QUtillity::decodeUnicodeString(json_encode($out));
		exit;
	}else{
		if($r[0]==0){ // 病患不存在, 須新增
			$need2UpdateWeight=false;
			$s="insert into MUST_QuestionnaireUser (OwnerID,No,Volition,Weight,LastDate) values (?,?,?,?,now())";
			$p=array($group_id,$p_id,1,$p_weight);
			if(!kwcr2_rawqueryexec($db, $s, $p, "")){
				$out[0]=array(900,"資料儲存失敗(1)！".vsprintf(str_replace('?','%s',$s),$p).kwcr2_geterrormsg($db,1).")");
				echo QUtillity::decodeUnicodeString(json_encode($out));
				exit;
			}
		}
	}

	$s1='OwnerID,Questionnaire,No,Answer,Weight,StaffID,Version';
	$s2='?,?,?,?,?,?,?';
	$p=array($group_id,$questionnaire,$p_id,json_encode($answer),$p_weight,$_SESSION['staffId'],CURRENT_VERSION);
	if($score!==null){$s1.=',Score'; $s2.=',?'; $p[]=$score;}
	if(!kwcr2_rawqueryexec($db, "insert into MUST_Questionnaire ($s1) values ($s2)", $p, "")){
		$out[0]=array(900,"資料儲存失敗(2)！(".kwcr2_geterrormsg($db,1).")");
		kwcr2_rollbacktransaction($db);
		echo QUtillity::decodeUnicodeString(json_encode($out));
		exit;
	}
	// 記最近體重
	if($need2UpdateWeight){
		$s="update MUST_QuestionnaireUser set Weight=? where No=?";
		$p=array($p_weight,$p_id);
		if(!kwcr2_rawqueryexec($db, $s, $p, "")){
			$out[0]=array(900,"資料儲存失敗(3)！(".kwcr2_geterrormsg($db,1).")");
			kwcr2_rollbacktransaction($db);
			echo QUtillity::decodeUnicodeString(json_encode($out));
			exit;
		}
	}

	kwcr2_committransaction($db);
}else{
	$out[0]=array(900,"資料庫連結失敗！");
//	return;
}
echo QUtillity::decodeUnicodeString(json_encode($out));
?>