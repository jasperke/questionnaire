﻿<?php
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

$fetch_range=array();
if(isset($start))
	$fetch_range['skip_rows']=(int)$start;
if(isset($size)&&$size!=0)
	$fetch_range['max_fetch_raw']=(int)$size;

// // 列表的條件, 檢視detail.php後才能回原來的列表
// $_SESSION['skip_rows']=$fetch_range['skip_rows'];
// $_SESSION['order']=($orderBy=='person')?1:0;

$out=array(array(0,''));
$list=array();
$group_id=2; // OwnerID
$dbid=2;

$SiteLoginUID=kwut2_readini(CFG_FN,"KOALA","SiteUID");
$SiteLoginPWD=kwut2_readini(CFG_FN,"KOALA","SitePWD");
$db=kwcr2_mapdb('CyberSite',$SiteLoginUID,$SiteLoginPWD);
if($db!=0){
	if(isset($no)){
		$s="select CreateTime,RandNum,No,Id,Name,Gender,Birthday,Email,Phone from MUST_QuestionnaireUser where OwnerID=? and No=?";
		$p=array($group_id,$no);
		$fetch_range=array('skip_rows'=>0,'max_fetch_raw'=>1);
	}else{
		$s="select CreateTime,RandNum,No,Id,Name,Gender,Birthday,Email,Phone from MUST_QuestionnaireUser where OwnerID=? order by CreateTime desc";
		$p=array($group_id);
	}
	$rs=read_multi_record($db, $s, $p, $fetch_range);
	if($rs===false){
		$out[0]=array(900,"資料讀取失敗！(".kwcr2_geterrormsg($db,1).")");
	}else if(isset($rs)){
		foreach($rs as $r)
			$out[]=$r;
	}
}else{
	$out[0]=array(900,"資料庫連結失敗！");
//	return;
}
echo json_encode($out);
?>