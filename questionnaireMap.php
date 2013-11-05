<?
// questionnaireMap暫時定義在程式碼內, 需與quizPool.js同步
// 日後要進db再來調整
$questionnaireMap=array();
$questionnaireMap["HN.COM"]=array('_HN1','_HN2','_HN3','_HN4','_HN5','_HN6','_HN7','_HN8','_HN9','_HN10',
	'_HN11','_HN12','_HN13','_HN14','_HN15','_HN16','_HN17','_HN18','_HN19','_HN20','_HN21','_HN22',
	'_PART_OF_PAIN','_SCORE_OF_PAIN');
$questionnaireMap["FACT-B"]=array('G1','G2','G3',
	'GP1','GP2','GP3','GP4','GP5','GP6','GP7',
	'GS1','GS2','GS3','GS4','GS5','GS6','GS7',
	'GE1','GE2','GE3','GE4','GE5','GE6',
	'GF1','GF2','GF3','GF4','GF5','GF6','GF7',
	'E1','E2','E3',
	'B1','B2','B3','B4','B5','B6','B7','B8','B9',
	'P2',
	'_PART_OF_PAIN','_SCORE_OF_PAIN');
$questionnaireMap["FACT-ECO"]=array('G1','G2','G3',
	'GP1','GP2','GP3','GP4','GP5','GP6','GP7',
	'GS1','GS2','GS3','GS4','GS5','GS6','GS7',
	'GE1','GE2','GE3','GE4','GE5','GE6',
	'GF1','GF2','GF3','GF4','GF5','GF6','GF7',
	'E1','E2','E3',
	'_PART_OF_PAIN','_SCORE_OF_PAIN');
$questionnaireMap["FACT-HN-X"]=array('G1','G2','G3',
	'GP1','GP2','GP3','GP4','GP5','GP6','GP7',
	'GS1','GS2','GS3','GS4','GS5','GS6','GS7',
	'GE1','GE2','GE3','GE4','GE5','GE6',
	'GF1','GF2','GF3','GF4','GF5','GF6','GF7',
	'H&N1','H&N2','H&N3','H&N4','H&N5','H&N6','H&N7','H&N8','H&N9','H&NA','H&NB',
	'X1','X2','X3','X4','X5','X6:X7,_X8,_X9,_X10,_X11,_X12,_X13,_X14,_X15',
	'E1','E2','E3',
	'_HN1','_HN2','_HN3','_HN4','_HN5','_HN6','_HN7','_HN8','_HN9','_HN10',
	'_HN11','_HN12','_HN13','_HN14','_HN15','_HN16','_HN17','_HN18','_HN19','_HN20',
	'_HN21','_HN22',
	'_PART_OF_PAIN','_SCORE_OF_PAIN');

function getQuestionnaire($q_id){
	global $questionnaireMap;

	if(isset($questionnaireMap[$q_id])){
		return json_encode($questionnaireMap[$q_id]);
	}else{
		return json_encode(array());
	}
}
if(isset($q_id)){ // 有傳q_id者, 為client讀取q_id問卷的題組
	header('Content-Type: application/json; charset=utf-8');
	echo getQuestionnaire($q_id);
	exit;
}
?>