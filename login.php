<?
session_start();

if(isset($logout)){
	unset($_SESSION['admin']);
}
if(isset($login_id)&&strcmp($login_id,'admin')==0&&isset($login_pw)&&strcmp($login_pw,'111111')==0){
	$_SESSION['admin']='changgung';
//	session_write_close();
	header('Location: ./index.php');
	exit;
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
              <input name="login_id" type="text" size="15">
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
