<?
session_start();

if(isset($logout)){
	unset($_SESSION['admin']);
}

if(isset($login_id,$login_pw)){
  require_once('koala.Utility.php');
  require_once('common.Utility.php');
  require_once('rpc.Utility2.php');
  define("CFG_FN", "/usr/local/koala/config.ini");

  $SiteLoginUID=kwut2_readini(CFG_FN,"KOALA","SiteUID");
  $SiteLoginPWD=kwut2_readini(CFG_FN,"KOALA","SitePWD");
  $db=kwcr2_mapdb('CyberSite',$SiteLoginUID,$SiteLoginPWD);
  if($db!=0){
    $s="select CreateTime,RandNum,Name from MUST_Staff where Account=? and Password=?";
    $p=array($login_id,$login_pw);
    $r=read_one_record($db, $s, $p);
    if($r===false||!isset($r)){

    }else{
      $_SESSION['admin']='changgung';
      $_SESSION['staffId']=$r[0].';'.$r[1];
      $_SESSION['doctor']=$r[2];
      header('Location: ./');
      exit;
    }
  }else{ // failed to connect to DB

  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
</head>

<body bgcolor="#EEEEEE" leftmargin="0" topmargin="0" onload="document.forms[0].login_id.focus();">
<form method="post" style="margin:0px;" action="<? echo htmlentities($_SERVER['PHP_SELF']); ?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="50" bgcolor="#000000">&nbsp;</td>
  </tr>
  <tr>
    <td height="100">&nbsp;</td>
  </tr>
  <tr>
    <td>
      <table width="491" height="278" border="0" align="center" cellpadding="0" cellspacing="0" background="images/login_bg.gif">
        <tr>
          <td><div align="center"><font color="#666666" size="6">長庚照護問卷系統</font></div></td>
        </tr>
        <tr>
          <td><div align="center"><font color="#333333">帳號：</font>
              <input name="login_id" type="text" size="15" autocomplete="off">
              <font color="#333333">密碼：</font>
              <input name="login_pw" type="password" size="15">
            </div></td>
        </tr>
        <tr>
          <td height="100">
            <div align="center">
              <input name="Submit" type="submit" value="登入">
            </div></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>
</body>
</html>
