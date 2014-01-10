<?
final class QUtillity{
	private static function _utf162utf8($utf16){
		// Check for mb extension otherwise do by hand.
		if( function_exists('mb_convert_encoding') ) {
			return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
		}
		$bytes = (ord($utf16{0}) << 8) | ord($utf16{1});
		switch (true) {
			case ((0x7F & $bytes) == $bytes):
				// this case should never be reached, because we are in ASCII range
				// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				return chr(0x7F & $bytes);
			case (0x07FF & $bytes) == $bytes:
				// return a 2-byte UTF-8 character
				// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				return chr(0xC0 | (($bytes >> 6) & 0x1F))
					 . chr(0x80 | ($bytes & 0x3F));
			case (0xFFFF & $bytes) == $bytes:
				// return a 3-byte UTF-8 character
				// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				return chr(0xE0 | (($bytes >> 12) & 0x0F))
					 . chr(0x80 | (($bytes >> 6) & 0x3F))
					 . chr(0x80 | ($bytes & 0x3F));
		}
		// ignoring UTF-32 for now, sorry
		return '';
	}
	public static function decodeUnicodeString($chrs){
		$delim       = substr($chrs, 0, 1);
		$utf8        = '';
		$strlen_chrs = strlen($chrs);

		for($i = 0; $i < $strlen_chrs; $i++) {
			$substr_chrs_c_2 = substr($chrs, $i, 2);
			$ord_chrs_c = ord($chrs[$i]);
			switch (true) {
				case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $i, 6)):
					// single, escaped unicode character
					$utf16 = chr(hexdec(substr($chrs, ($i + 2), 2)))
						   . chr(hexdec(substr($chrs, ($i + 4), 2)));
					$utf8 .= self::_utf162utf8($utf16);
					$i += 5;
					break;
				case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
					$utf8 .= $chrs{$i};
					break;
				case ($ord_chrs_c & 0xE0) == 0xC0:
					// characters U-00000080 - U-000007FF, mask 110XXXXX
					//see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
					$utf8 .= substr($chrs, $i, 2);
					++$i;
					break;
				case ($ord_chrs_c & 0xF0) == 0xE0:
					// characters U-00000800 - U-0000FFFF, mask 1110XXXX
					// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
					$utf8 .= substr($chrs, $i, 3);
					$i += 2;
					break;
				case ($ord_chrs_c & 0xF8) == 0xF0:
					// characters U-00010000 - U-001FFFFF, mask 11110XXX
					// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
					$utf8 .= substr($chrs, $i, 4);
					$i += 3;
					break;
				case ($ord_chrs_c & 0xFC) == 0xF8:
					// characters U-00200000 - U-03FFFFFF, mask 111110XX
					// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
					$utf8 .= substr($chrs, $i, 5);
					$i += 4;
					break;
				case ($ord_chrs_c & 0xFE) == 0xFC:
					// characters U-04000000 - U-7FFFFFFF, mask 1111110X
					// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
					$utf8 .= substr($chrs, $i, 6);
					$i += 5;
					break;
			}
		}
		return $utf8;
	}
}

final class Calculator{
	static $GP=array(); // GP1~GP7 生理
	static $GS=array(); // GS1~GS7 社會/家庭
	static $GE=array(); // GE1~GE6 情緒
	static $GF=array(); // GF1~GF7 功能
	static $HN=array(); // H&N1~H&N11 H&N
	static $B=array(); // B1~B9,P2 Breast附加
	static $NP=array(); // _X8~_X15 NP附加
	static $detail=array(); // 各類小計, 及失眠/疲勞/疼痛分數

	private function __construct(){

	}
	public static function reset(){
		self::$GP=array();
		self::$GS=array();
		self::$GE=array();
		self::$GF=array();
		self::$HN=array();
		self::$B=array();
		self::$NP=array();
		self::$detail=array();
	}
/*
	public static function inputAnswer($q_id,$answer){
		if(in_array($q_id,array('GP1','GP2','GP3','GP4','GP5','GP6','GP7'))){ // 全必填
			//$idx=(int)substr($q_id,2,1);
			$idx=array_search($q_id,array('GP1','GP2','GP3','GP4','GP5','GP6','GP7'));
			self::$GP[$idx]=4-(int)$answer;
		}else if(in_array($q_id,array('GS1','GS2','GS3','GS4','GS5','GS6','GS7'))){
			if($q_id=='GS7'&&$answer==5){ // GS7特殊題, 允許答5:不想回答
				// 不必記入$GS array, 但之後除以'回答的題數'=6
			}else{
				//$idx=(int)substr($q_id,2);
				$idx=array_search($q_id,array('GS1','GS2','GS3','GS4','GS5','GS6','GS7'));
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
		// }else if(in_array($q_id,array('_HN1','_HN2','_SCORE_OF_PAIN'))){
		// 	self::$detail[$q_id]=$answer;
		}else if(in_array($q_id,array('B1','B2','B3','B4','B5','B6','B7','B8','B9','P2'))){


		}
	}
*/
	public static function inputAnswer($q_id,$answer){
		if(($idx=array_search($q_id,array('GP1','GP2','GP3','GP4','GP5','GP6','GP7')))!==false){ // 全必填
			self::$GP[$idx]=4-(int)$answer; // GP用4分倒扣
		}else if(($idx=array_search($q_id,array('GS1','GS2','GS3','GS4','GS5','GS6','GS7')))!==false){
			if($q_id=='GS7'&&$answer==5){ // GS7特殊題, 允許答5:不想回答
				// 不必記入$GS array, 但之後除以'回答的題數'=6
			}else{ // GS用0分往上加
				self::$GS[$idx]=(int)$answer;
			}
		}else if(($idx=array_search($q_id,array('GE1','GE2','GE3','GE4','GE5','GE6')))!==false){ // 全必填
			if($idx==1){  // GE2用0分往上加
				self::$GE[$idx]=(int)$answer;
			}else{ // 其餘用4分倒扣
				self::$GE[$idx]=4-(int)$answer;
			}
		}else if(($idx=array_search($q_id,array('GF1','GF2','GF3','GF4','GF5','GF6','GF7')))!==false){ // 全必填
			self::$GF[$idx]=(int)$answer; // GF用0分往上加

		}else if(($idx=array_search($q_id,array('H&N1','H&N2','H&N3','H&N4','H&N5','H&N6','H&N7','H&N10','H&N11')))!==false){
			if($idx==1||$idx==2||$idx==5){ // H&N2, H&N3, H&N6用4分倒扣
				self::$HN[$idx]=4-(int)$answer;
			}else{ // 其餘用0分往上加
				self::$HN[$idx]=(int)$answer;
			}
		// }else if(in_array($q_id,array('_HN1','_HN2','_SCORE_OF_PAIN'))){
		// 	self::$detail[$q_id]=$answer;
		}else if(($idx=array_search($q_id,array('B1','B2','B3','B4','B5','B6','B7','B8','B9','P2')))!==false){
			if($idx==3||$idx==8){ // B4, B9用0分往上加
				self::$B[$idx]=(int)$answer;
			}else{ // 其餘用4份倒扣
				self::$B[$idx]=4-(int)$answer;
			}
		}else if(($idx=array_search($q_id,array('_X8','_X9','_X10','_X11','_X12','_X13','_X14','_X15')))!==false){
			if($idx==6){ // _X14
				self::$NP[$idx]=(int)$answer;
			}else{
				self::$NP[$idx]=4-(int)$answer;
			}
		}
	}
	public static function outSum(){
		// FACT總分=GP總分+GS總分+GE總分+GF總分
		$sum=0;
		$needScore=false;
		// [題目分數的加總]*[題數]/[回答的題數]
		if(count(self::$GP)){
			$needScore=true;
			self::$detail['GP']=array_sum(self::$GP);
			$sum+=self::$detail['GP'];
		}
		if(count(self::$GS)){
			$needScore=true;
			self::$detail['GS']=array_sum(self::$GS)*7/count(self::$GS); // GS7不答的話, 題數與回答題數不會相等, 須乘除
			$sum+=self::$detail['GS'];
		}
		if(count(self::$GE)){
			$needScore=true;
			self::$detail['GE']=array_sum(self::$GE);
			$sum+=self::$detail['GE'];
		}
		if(count(self::$GF)){
			$needScore=true;
			self::$detail['GF']=array_sum(self::$GF);
			$sum+=self::$detail['GF'];
		}
		if(count(self::$HN)){ // HN不列入FACT總分
			//$needScore=true;
			self::$detail['H&N']=array_sum(self::$HN);
			//$sum+=self::$detail['H&N'];
		}
		if(count(self::$B)){ // B不列入FACT總分
			self::$detail['B']=array_sum(self::$B);
		}
		if(count(self::$NP)){ // NP不列入FACT總分
			self::$detail['NP']=array_sum(self::$NP);
		}
		self::$detail['SUM']=$needScore?$sum:null;

		return self::$detail;
	}
}
?>