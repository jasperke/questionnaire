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
		$s="update MUST_QuestionnaireUser set Name=?,Email=?,Phone=?,Cancer=?,Memo=?";
		$p=array($patient_name,$email,$phone,json_encode($cancer),$memo);
		if(isset($gender)){
			$s.=",Gender=?";
			$p[]=$gender;
		}else{
			$s.=",Gender=null";
		}
		if(isset($birthday)&&strcmp($birthday,'')!=0){
			$s.=",Birthday=?";
			$p[]=$birthday;
		}else{
			$s.=",Birthday=null";
		}
		if(isset($first_date)&&strcmp($first_date,'')!=0){
			$s.=",FirstDate=?";
			$p[]=$first_date;
		}else{
			$s.=",FirstDate=null";
		}
		if(isset($last_date)&&strcmp($last_date,'')!=0){
			$s.=",LastDate=?";
			$p[]=$last_date;
		}else{
			$s.=",LastDate=null";
		}
		if(isset($caregiver)&&strcmp($caregiver,'')!=0){
			$s.=",Caregiver=?";
			$p[]=$caregiver;
		}
		if(isset($volition)&&strcmp($volition,'')!=0){
			$s.=",Volition=?";
			$p[]=$volition;
		}
		$s.=" where Createtime=? and RandNum=?";
		array_push($p,$gukey[0],$gukey[1]);
	}else{
		$s1='OwnerID,No,Cancer,Memo';
		$s2='?,?,?,?';
		$p=array($group_id,$no,json_encode($cancer),$memo);
		if(isset($patient_name)){$s1.=',Name'; $s2.=',?'; $p[]=$patient_name;}
		if(isset($gender)){$s1.=',Gender'; $s2.=',?'; $p[]=$gender;}
		if(isset($birthday)&&strcmp($birthday,'')!=0){$s1.=',Birthday'; $s2.=',?'; $p[]=$birthday;}
		if(isset($email)){$s1.=',Email'; $s2.=',?'; $p[]=$email;}
		if(isset($phone)){$s1.=',Phone'; $s2.=',?'; $p[]=$phone;}
		if(isset($first_date)&&strcmp($first_date,'')!=0){$s1.=',FirstDate'; $s2.=',?'; $p[]=$first_date;}
		if(isset($last_date)&&strcmp($last_date,'')!=0){$s1.=',LastDate'; $s2.=',?'; $p[]=$last_date;}
		if(isset($caregiver)&&strcmp($caregiver,'')!=0){$s1.=',Caregiver'; $s2.=',?'; $p[]=$caregiver;}
		if(isset($volition)&&strcmp($volition,'')!=0){$s1.=',Volition'; $s2.=',?'; $p[]=$volition;}
		$s="insert into MUST_QuestionnaireUser ($s1) values ($s2)";
	}
//error_log("\n".vsprintf(str_replace('?','%s',$s),$p), 3,'/tmp/jasper.log');
	if(!kwcr2_rawqueryexec($db, $s, $p, "")){
		$out[0]=array(900,"資料儲存失敗！".vsprintf(str_replace('?','%s',$s),$p).kwcr2_geterrormsg($db,1).")");
	}
}else{
	$out[0]=array(900,"資料庫連結失敗！");
//	return;
}
echo QUtillity::decodeUnicodeString(json_encode($out));
?>