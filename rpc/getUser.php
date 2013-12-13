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

$fetch_range=array();
if(isset($start))
	$fetch_range['skip_rows']=(int)$start;
if(isset($size)&&$size!=0)
	$fetch_range['max_fetch_raw']=(int)$size;
if(!isset($filterNo))
	$filterNo='';
if(!isset($filterName))
	$filterName='';
if(!isset($filterBirthday))
	$filterBirthday='';

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
		$s="select CreateTime,RandNum,No,Name,Gender,Birthday,Email,Phone,Weight,Cancer,Volition,Caregiver,FirstDate,LastDate,Memo from MUST_QuestionnaireUser where OwnerID=? and No=?";
		$p=array($group_id,$no);
		$fetch_range=array('skip_rows'=>0,'max_fetch_raw'=>1);
	}else{
		if(isset($total_count)&&strcmp($total_count,'1')==0){ // 須回傳總筆數
			$s="select count(*) from MUST_QuestionnaireUser where OwnerID=?";
			$p=array($group_id);
			if(strcmp($filterNo,'')!=0){
				$s.=" and SUBSTRING(No,1,?)=?";
				array_push($p,strlen($filterNo),$filterNo);
			}
			if(strcmp($filterName,'')!=0){
				$s.=" and Name like '%".$filterName."%'";
			}
			if(strcmp($filterBirthday,'')!=0){
				$s.=" and Birthday=?";
				array_push($p,$filterBirthday);
			}
			$r=read_one_record($db, $s, $p);
			if($r===false||!isset($r)){
				$out[0]=array(900,"讀取資料失敗(0)！".kwcr2_geterrormsg($db, 1));
				echo QUtillity::decodeUnicodeString(json_encode($out));
				exit;
			}else{
				$out[1]=array($r[0]);
			}
		}
		$s="select CreateTime,RandNum,No,Name,Gender,Birthday,Email,Phone,Weight,Cancer,Volition,Caregiver,FirstDate,LastDate,Memo from MUST_QuestionnaireUser where OwnerID=?";
		$p=array($group_id);
		if(strcmp($filterNo,'')!=0){
			$s.=" and SUBSTRING(No,1,?)=?";
			array_push($p,strlen($filterNo),$filterNo);
		}
		if(strcmp($filterName,'')!=0){
			$s.=" and Name like '%".$filterName."%'";
		}
		if(strcmp($filterBirthday,'')!=0){
			$s.=" and Birthday=?";
			array_push($p,$filterBirthday);
		}
 		$s.=" order by CreateTime desc";
	}
	$rs=read_multi_record($db, $s, $p, $fetch_range);
	if($rs===false){
		$out[0]=array(900,"資料讀取失敗(1)！(".kwcr2_geterrormsg($db,1).")");
	}else if(isset($rs)){
		$patients=array(); // 記病歷號
		foreach($rs as $r){
			$patients[]=$r[2];
			if(strcmp($r[9],'')==0){ // 癌別json字串須轉成array
				$r[9]=array();
			}else{
				$r[9]=json_decode($r[9]);
			}
			$out[]=$r;
		}

		// 找出各做過哪幾種問卷
		$questionnaires=array();
		if(count($patients)){
			$s="select distinct No,Questionnaire from MUST_Questionnaire where NO in ('".implode("','",$patients)."')";
			$rs=read_multi_record($db, $s, array());
			if($rs===false){
				$out[0]=array(900,"資料讀取失敗(2)！$s (".kwcr2_geterrormsg($db,1).")");
			}else if(isset($rs)){
				foreach($rs as $r){
					if(!isset($questionnaires[$r[0]]))
						$questionnaires[$r[0]]=array();
					$questionnaires[$r[0]][]=$r[1];
				}
			}
		}
		foreach($out as &$o)
			$o[]=isset($questionnaires[$o[2]])?$questionnaires[$o[2]]:array();
	}
}else{
	$out[0]=array(900,"資料庫連結失敗！");
//	return;
}
echo QUtillity::decodeUnicodeString(json_encode($out));
?>