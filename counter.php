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

final class Calculator{
	static $GP=array(); // GP1~GP7
	static $GS=array(); // GS1~GS7
	static $GE=array(); // GE1~GE6
	static $GF=array(); // GF1~GF7
	static $HN=array(); // H&N1~H&N11

	private function __construct(){

	}
	public static function inputAnswer($q_id,$answer){
		if(in_array($q_id,array('GP1','GP2','GP3','GP4','GP5','GP6','GP7'))){ // 全必填
			$idx=(int)substr($q_id,2,1);
			self::$GP[$idx]=4-(int)$answer;
		}else if(in_array($q_id,array('GS1','GS2','GS3','GS4','GS5','GS6','GS7'))){
			if($q_id=='GS7'&&$answer==5){ // GS7特殊題, 允許答5:不想回答
				// 不必記入$GS array, 但之後除以'回答的題數'=6
			}else{
				$idx=(int)substr($q_id,2);
				self::$GS[$idx]=(int)$answer;
			}
		}else if(in_array($q_id,array('GE1','GE2','GE3','GE4','GE5','GE6'))){ // 全必填
			$idx=(int)substr($q_id,2);
			if($idx==2){
				self::$GE[$idx]=(int)$answer;
			}else{
				self::$GE[$idx]=4-(int)$answer;
			}
		}else if(in_array($q_id,array('GF1','GF2','GF3','GF4','GF5','GF6','GF7'))){ // 全必填
			$idx=(int)substr($q_id,2);
			self::$GF[$idx]=(int)$answer;
		}else if(in_array($q_id,array('H&N1','H&N4','H&N5','H&N7','H&N10','H&N11'))){
			$idx=(int)substr($q_id,3);
			self::$HN[$idx]=(int)$answer;
		}else if(in_array($q_id,array('H&N2','H&N3','H&N6'))){
			$idx=(int)substr($q_id,3);
			self::$HN[$idx]=4-(int)$answer;
		}
	}
	public static function outSum(){
		$sum=0;
		$needScore=false;
		// [題目分數的加總]*[題數]/[回答的題數]
		if(count(self::$GP)){
			$needScore=true;
			$sum+=array_sum(self::$GP);
		}
		if(count(self::$GS)){
			$needScore=true;
			$sum+=array_sum(self::$GS)*7/count(self::$GS); // GS7不答的話, 題數與回答題數不會相等, 須乘除
		}
		if(count(self::$GE)){
			$needScore=true;
			$sum+=array_sum(self::$GE);
		}
		if(count(self::$GF)){
			$needScore=true;
			$sum+=array_sum(self::$GF);
		}
		if(count(self::$HN)){
			$needScore=true;
			$sum+=array_sum(self::$HN);
		}
		return $needScore?$sum:null;
	}
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
	list($q_id,$sub_q)=explode(':',$questionnaireMap[$questionnaire][$idx]);
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
$score=Calculator::outSum();

$out=array(array(0,''));
$group_id=2; // OwnerID
$dbid=2;

$SiteLoginUID=kwut2_readini(CFG_FN,"KOALA","SiteUID");
$SiteLoginPWD=kwut2_readini(CFG_FN,"KOALA","SitePWD");
$db=kwcr2_mapdb('CyberSite',$SiteLoginUID,$SiteLoginPWD);
if($db!=0){
	if($score===null){
		$s="insert into MUST_Questionnaire (OwnerID,Questionnaire,No,Answer,Weight) values (?,?,?,?,?)";
		$p=array($group_id,$questionnaire,$p_id,json_encode($answer),$p_weight);
	}else{
		$s="insert into MUST_Questionnaire (OwnerID,Questionnaire,No,Answer,Score,Weight) values (?,?,?,?,?,?)";
		$p=array($group_id,$questionnaire,$p_id,json_encode($answer),$score,$p_weight);
	}
	if(!kwcr2_rawqueryexec($db, $s, $p, "")){
		$out[0]=array(900,"資料儲存失敗！(".kwcr2_geterrormsg($db,1).")");
//		return;
	}
}else{
	$out[0]=array(900,"資料庫連結失敗！");
//	return;
}
echo QUtillity::decodeUnicodeString(json_encode($out));
?>