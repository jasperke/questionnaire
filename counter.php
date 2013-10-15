<?php
//counter.php
require_once('koala.Utility.php');
require_once('common.Utility.php');
require_once('rpc.Utility2.php');
//require_once('ChangGung.Utility.php');
define("CFG_FN", "/usr/local/koala/config.ini");

//$CalculateRule=array('GP1'=>)
function method1(){
	
}



/*
$questionnaire="HN.COM";
$p_id="898989";
$p_name="hjlkjl";
$p_gender="1";
$answer=["0","1","1","2","1","3","1","0","2","1","0","1","2","1","2","3","0","1","2","1","0","1"];
$quizzes=["_HN1","_HN2","_HN3","_HN4","_HN5","_HN6","_HN7","_HN8","_HN9","_HN10","_HN11","_HN12","_HN13","_HN14","_HN15","_HN16","_HN17","_HN18","_HN19","_HN20","_HN21","_HN22"];
*/

$out=array(array(0,''));
$group_id=2; // OwnerID
$dbid=2;

$SiteLoginUID=kwut2_readini(CFG_FN,"KOALA","SiteUID");
$SiteLoginPWD=kwut2_readini(CFG_FN,"KOALA","SitePWD");
$db=kwcr2_mapdb('CyberSite',$SiteLoginUID,$SiteLoginPWD);
if($db!=0){
	if(!isset($score)){
		$s="insert into MUST_Questionnaire (OwnerID,Questionnaire,No,Answer) values (?,?,?,?)";
		$p=array($group_id,$questionnaire,$p_id,json_encode($answer));
	}else{
		$s="insert into MUST_Questionnaire (OwnerID,Questionnaire,No,Answer,Score) values (?,?,?,?,?)";
		$p=array($group_id,$questionnaire,$p_id,$answer,$score);
	}
	if(!kwcr2_rawqueryexec($db, $s, $p, "")){
		$out[0]=array(900,"資料儲存失敗！(".kwcr2_geterrormsg($db,1).")");
//		return;
	}
}else{
	$out[0]=array(900,"資料庫連結失敗！");
//	return;
}
echo json_encode($out);
?>