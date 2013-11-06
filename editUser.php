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

if(!isset($no)||strcmp($no,'')==0){
	$out=array(array(903,"缺必要參數（病歷號）！"));
	echo QUtillity::decodeUnicodeString(json_encode($out));
	exit;
}

$out=array(array(0,''));
$group_id=2; // OwnerID
$dbid=2;

$SiteLoginUID=kwut2_readini(CFG_FN,"KOALA","SiteUID");
$SiteLoginPWD=kwut2_readini(CFG_FN,"KOALA","SitePWD");
$db=kwcr2_mapdb('CyberSite',$SiteLoginUID,$SiteLoginPWD);
if($db!=0){
	if(isset($patient_id)&&strcmp($patient_id,'')!=0){ // edit
		$gukey=global_unique_key_decode($patient_id);
		$s="update MUST_QuestionnaireUser set Id=?,Name=?,Gender=?,Email=?,Phone=?";
		$p=array($id_no,$patient_name,$gender,$email,$phone);
		if(isset($birthday)){
			$s.=",Birthday=?";
			$p[]=$birthday;
		}else{
			$s.=",Birthday=null";
		}
		$s.=" where Createtime=? and RandNum=?";
		array_push($p,$gukey[0],$gukey[1]);
	}else{
		$s1='OwnerID,No';
		$s2='?,?';
		$p=array($group_id,$no);
		if(isset($id_no)){$s1.=',Id'; $s2.=',?'; $p[]=$id_no;}
		if(isset($patient_name)){$s1.=',Name'; $s2.=',?'; $p[]=$patient_name;}
		if(isset($gender)){$s1.=',Gender'; $s2.=',?'; $p[]=$gender;}
		if(isset($birthday)){$s1.=',Birthday'; $s2.=',?'; $p[]=$birthday;}
		if(isset($email)){$s1.=',Email'; $s2.=',?'; $p[]=$email;}
		if(isset($phone)){$s1.=',Phone'; $s2.=',?'; $p[]=$phone;}
		$s="insert into MUST_QuestionnaireUser ($s1) values ($s2)";
	}
	if(!kwcr2_rawqueryexec($db, $s, $p, "")){
		$out[0]=array(900,"資料儲存失敗！".kwcr2_geterrormsg($db,1).")");
	}
}else{
	$out[0]=array(900,"資料庫連結失敗！");
//	return;
}
echo QUtillity::decodeUnicodeString(json_encode($out));
?>