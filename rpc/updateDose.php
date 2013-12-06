<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once('koala.Utility.php');
require_once('common.Utility.php');
require_once('rpc.Utility2.php');
//require_once('questionnaireMap.php');
require_once('questionnaireUtility.php');
define("CFG_FN", "/usr/local/koala/config.ini");

if(!isset($_SESSION['admin'])||strcmp($_SESSION['admin'],'changgung')!=0){ // 未登入
	$out=array(array(902,"無權限使用！"));
	echo QUtillity::decodeUnicodeString(json_encode($out));
	exit;
}
if(!isset($id,$dose)||strcmp($id,'')==0){
	$out=array(array(902,"參數錯誤(id,dose)！(1)"));
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
	// if(!kwcr2_starttransaction($db)){
	// 	$out[0]=array(900,"failed to start transaction!(".kwcr2_geterrormsg($db,1).")");
	// 	echo QUtillity::decodeUnicodeString(json_encode($out));
	// 	exit;
	// }
	$gukey=global_unique_key_decode($id);
	$s="select count(*) from MUST_Questionnaire where OwnerID=? and CreateTime=? and RandNum=?";
	$p=array($group_id, $gukey[0], $gukey[1]);
	$r=read_one_record($db, $s, $p);
	if($r===false||!isset($r)){
		$out[0]=array(900,"資料儲存失敗(1)！".kwcr2_geterrormsg($db, 1));
		echo QUtillity::decodeUnicodeString(json_encode($out));
		exit;
	}else{
		if($r[0]==0){
			$out[0]=array(900,"參數錯誤(id)！(2)");
			echo QUtillity::decodeUnicodeString(json_encode($out));
			exit;
		}
	}

	$s="update MUST_Questionnaire set Dose=? where OwnerID=? and CreateTime=? and RandNum=?";
	$p=array(json_encode($dose), $group_id, $gukey[0], $gukey[1]);
	if(!kwcr2_rawqueryexec($db, $s, $p, "")){
		$out[0]=array(900,"資料儲存失敗(2)！(".kwcr2_geterrormsg($db,1).")");
		//kwcr2_rollbacktransaction($db);
		echo QUtillity::decodeUnicodeString(json_encode($out));
		exit;
	}

	//kwcr2_committransaction($db);
}else{
	$out[0]=array(900,"資料庫連結失敗！");
//	return;
}
echo QUtillity::decodeUnicodeString(json_encode($out));
?>