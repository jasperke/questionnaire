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

if(!isset($scopeMode))
	$scopeMode=1; // 0:all, 1:waiting
$_SESSION['scope']=$scopeMode;

$out=array(array(0,''));
$list=array();
$group_id=2; // OwnerID
$dbid=2;

$SiteLoginUID=kwut2_readini(CFG_FN,"KOALA","SiteUID");
$SiteLoginPWD=kwut2_readini(CFG_FN,"KOALA","SitePWD");
$db=kwcr2_mapdb('CyberSite',$SiteLoginUID,$SiteLoginPWD);
if($db!=0){
	if(strcmp($scopeMode,'0')==0){ // 今日全部病患
		$s="select q.CreateTime,q.RandNum,q.Questionnaire,q.No,q.Score,u.Name,q.Weight,u.Gender,u.Birthday from MUST_Questionnaire q left outer join MUST_QuestionnaireUser u on q.No=u.No where q.OwnerID=? and q.StaffID=? and DATEPART(q.CreateTime)=CURDATE() order by q.CreateTime asc";
	}else{ // 今日待診病患
		$s="select q.CreateTime,q.RandNum,q.Questionnaire,q.No,q.Score,u.Name,q.Weight,u.Gender,u.Birthday from MUST_Questionnaire q left outer join MUST_QuestionnaireUser u on q.No=u.No where q.OwnerID=? and q.StaffID=? and DATEPART(q.CreateTime)=CURDATE() and Dose is null order by q.CreateTime asc";
	}
	$rs=read_multi_record($db, $s, array($group_id, $_SESSION['staffId']));
	if($rs===false){
		$out[0]=array(900,"資料讀取失敗！(".kwcr2_geterrormsg($db,1).")");
	}else if(isset($rs)){
		foreach($rs as $r)
			$out[]=$r;
	}
}else{
	$out[0]=array(900,"資料庫連結失敗！");
}
echo json_encode($out);
?>