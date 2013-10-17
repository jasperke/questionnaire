<?php
session_start();

if(!isset($_SESSION['admin'])||strcmp($_SESSION['admin'],'changgung')!=0){ // 未登入
	header('Location: ./login.php');
	exit;
}else{
	if(!isset($questionnaire)||!isset($questionnaireMap[$questionnaire])){
		header('Location: ./index.php');
		exit;
	}
}

require_once('koala.Utility.php');
require_once('common.Utility.php');
require_once('rpc.Utility2.php');
//require_once('questionnaireMap.php');
//require_once('ChangGung.Utility.php');
define("CFG_FN", "/usr/local/koala/config.ini");


$fetch_range=array();
if(isset($start))
	$fetch_range['skip_rows']=(int)$start;
if(isset($size)&&$size!=0)
	$fetch_range['max_fetch_raw']=(int)$size;
if(!isset($order))
	$order='time';

// 列表的條件, 檢視detail.php後才能回原來的列表
$_SESSION['skip_rows']=$fetch_range['skip_rows'];
$_SESSION['order']=($order=='person')?1:0;

$out=array(array(0,''));
$list=array();
$group_id=2; // OwnerID
$dbid=2;

$SiteLoginUID=kwut2_readini(CFG_FN,"KOALA","SiteUID");
$SiteLoginPWD=kwut2_readini(CFG_FN,"KOALA","SitePWD");
$db=kwcr2_mapdb('CyberSite',$SiteLoginUID,$SiteLoginPWD);
if($db!=0){
	if($order=='person'){
		$s="select CreateTime,RandNum,Questionnaire,No,Score,Name,Weight from MUST_Questionnaire where OwnerID=? order by No";
	}else{
		$s="select CreateTime,RandNum,Questionnaire,No,Score,Name,Weight from MUST_Questionnaire where OwnerID=? order by CreateTime desc";
	}
	$rs=read_multi_record($db, $s, array($group_id), $fetch_range);
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