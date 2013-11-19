<?php
session_start();
//require_once('questionnaireMap.php');

if(!isset($_SESSION['admin'])||strcmp($_SESSION['admin'],'changgung')!=0){ // 未登入
  header('Location: ./login.php');
  exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<script type="text/javascript" src="js/jquery.min.js" ></script>
<script type="text/javascript" src="js/underscore-min.js" ></script>
<script>
var start=<? echo isset($_SESSION['skip_rows'])?$_SESSION['skip_rows']:0; ?>,
  order='<? echo isset($_SESSION['order'])?$_SESSION['order']:0; ?>',
  page_size=0, // 小周說先不作分頁
  row_template;

function getList(){
  $.ajax({
    url:'getList.php',
    dataType:'json',
    type:'POST',
    data:{
      start:start,
      size:page_size,
      orderBy:document.forms[0].orderBy.options[document.forms[0].orderBy.selectedIndex].value
    },
    error:function(){
      alert('error'); // TODO:
    },
    success:function(data){
      if(data[0][0]!=0){
        alert('錯誤！\n\n錯誤代碼：'+data[0][0]+'\n錯誤訊息：'+data[0][1]);
      }else{
        tableBuilder(data);
      }
    }
  });
}
function tableBuilder(data){
  var listHere=$('#listHere');
  listHere.find('table:gt(0)').remove();
  for(var i=1; i<data.length; i++){
    listHere.append(
      row_template({
        idx:i+start,
        id:data[i][0]+';'+data[i][1],
        patient_no:data[i][3],
        q_id:data[i][2],
        score:data[i][4],
        time:data[i][0].substr(0,16)
      }));
  }
}
$(function(){
  document.forms[0].orderBy.selectedIndex=order;
  row_template=_.template($("#row_template").html());
  getList();
  $('#listHere').on('click','tr.row',function(){
    window.location.replace('detail.php?id='+$(this).data('id'));
  });
})
</script>
</head>
<body bgcolor="#EEEEEE" leftmargin="0" topmargin="0">
<form>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="137" height="50" bgcolor="#000000">&nbsp;</td>
    <td width="900" bgcolor="#000000">t<font color="#999999" size="4">長庚問卷調查系統</font></td>
    <td width="83" bgcolor="#000000"><a href="./"><font color="#999999" size="4">主選單</font></a></td>
    <td width="153" bgcolor="#000000"><select name="orderBy" onchange="start=0;getList();">
        <option selected value="time">依作答時間呈現</option>
        <option value="person">依人員呈現</option>
      </select></td>
    <td width="78" bgcolor="#000000"><a href="login.php?logout=1"><font color="#999999" size="4">登出</font></a></td>
  </tr>
  <tr>
    <td colspan="5" id="listHere">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr bgcolor="#CCCCCC">
          <td width="4%" height="30"> <div align="center"><font size="4" face="Arial, Helvetica, sans-serif">No.</font></div></td>
          <td width="14%"> <div align="center"><font size="4">病例號</font></div></td>
          <td width="58%"> <div align="center"><font size="4">問卷名稱</font></div></td>
          <td width="8%"> <div align="center"><font size="4">總分</font></div></td>
          <td width="16%"> <div align="center"><font size="4">填寫時間</font></div></td>
        </tr>
        <tr bgcolor="#666666">
          <td colspan="5"><img src="images/dot.gif" width="1" height="1"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="5">&nbsp;</td>
  </tr>
</table>
</form>

<script type="text/template" id="row_template">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="row" data-id="<%= id %>" style="cursor:pointer;"><td width="4%" height="30" bgcolor="#FFFFFF"><div align="center"><font color="#555555" face="Arial, Helvetica, sans-serif"><%= idx %></font></div></td>
      <td width="14%" bgcolor="#FFFFFF"><div align="center"><font color="#555555" face="Arial, Helvetica, sans-serif"><%= patient_no %></font></div></td>
      <td width="58%" bgcolor="#FFFFFF"><div align="center"><font face="Arial, Helvetica, sans-serif" color="#555555"><%= q_id %></font></div></td>
      <td width="8%" bgcolor="#FFFFFF"><div align="center"><font face="Arial, Helvetica, sans-serif" color="#555555"><%= score %></font></div></td>
      <td width="16%" bgcolor="#FFFFFF"><div align="center"><font color="#555555" face="Arial, Helvetica, sans-serif"><%= time %></font></div></td>
    </tr>
    <tr><td colspan="6" bgcolor="#dddddd"><img src="images/dot.gif" width="1" height="1"></td></tr>
  </table>
</script>
</body>
</html>
