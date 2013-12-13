<?php
session_start();

if(!isset($_SESSION['admin'])||strcmp($_SESSION['admin'],'changgung')!=0){ // 未登入
  header('Location: ./login.php');
  exit;
}
$scope=isset($_SESSION['scope'])?$_SESSION['scope']:1; // 0:all, 1:waiting
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<style type="text/css">
<!--
table.listTable td {font-size:24px;}
-->
</style>
<script type="text/javascript" src="js/jquery.min.js" ></script>
<script type="text/javascript" src="js/underscore-min.js" ></script>
<script src="js/main.min.js" ></script>
<script>
var start=<? echo isset($_SESSION['skip_rows'])?$_SESSION['skip_rows']:0; ?>,
  scope='<? echo $scope; ?>',
  row_template;

function getList(){
  $.ajax({
    url:'rpc/getList.php',
    dataType:'json',
    type:'POST',
    data:{
      scopeMode: document.forms[0].scope.options[document.forms[0].scope.selectedIndex].value
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
    listHere.append( // CreateTime,RandNum,Questionnaire,No,Score,Name,Weight,Gender,Birthday
      row_template({
        idx:i+start,
        id:data[i][0]+';'+data[i][1],
        q_id:data[i][2],
        patient_no:data[i][3],
        score:data[i][4],
        patient_name:data[i][5],
        gender:data[i][7]==1?'男':(data[i][6]==2?'女':''),
        birthday:toEra(data[i][8],true),
        time:data[i][0].substr(11,8)
      }));
  }
}
function viewQuestionnaire(id){
  window.open('detail.php?id='+id);
}
function viewReport(pid,quest){
  window.open('report.php?pid='+pid+'&quest='+quest);
}
$(function(){
  document.forms[0].scope.selectedIndex=scope;
  row_template=_.template($("#row_template").html());
  getList();
  // $('#listHere').on('click','tr.row',function(){
  //   window.location.replace('detail.php?id='+$(this).data('id'));
  // });
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
    <td width="153" bgcolor="#000000">
      <select name="scope" onchange="getList();">
        <option value="0">今日全部</option>
        <option selected value="1">等待看診</option>
      </select></td>
    <td width="78" bgcolor="#000000"><a href="login.php?logout=1"><font color="#999999" size="4">登出</font></a></td>
  </tr>
  <tr>
    <td colspan="5" id="listHere">
      <table class="listTable" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr bgcolor="#CCCCCC">
          <td width="4%" height="45"> <div align="center"><font size="4" face="Arial, Helvetica, sans-serif">No.</font></div></td>
          <td width="13%"><div align="center"><font size="4">病例號</font></div></td>
          <td width="9%"><div align="center"><font size="4">姓名</font></div></td>
          <td width="6%"><div align="center"><font size="4">性別</font></div></td>
          <td width="21%"><div align="center"><font size="4">出生日期</font></div></td>
          <td width="16%"> <div align="center"><font size="4">問卷名稱</font></div></td>
          <td width="6%"> <div align="center"><font size="4">總分</font></div></td>
          <td width="11%"> <div align="center"><font size="4">填寫時間</font></div>
          <td width="14%"><div><font size="4">&nbsp;</font></div></td></tr>
        <tr bgcolor="#666666">
          <td colspan="9"><img src="images/dot.gif" width="1" height="1"></td>
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
  <table class="listTable" width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="row" data-id="<%= id %>"><td width="4%" height="45" bgcolor="#FFFFFF"><div align="center"><font color="#555555" face="Arial, Helvetica, sans-serif"><%= idx %></font></div></td>
      <td width="13%" bgcolor="#FFFFFF"><div align="center"><font color="#555555" face="Arial, Helvetica, sans-serif"><%= patient_no %></font></div></td>
      <td width="9%" bgcolor="#FFFFFF"><div align="center"><font color="#555555" face="Arial, Helvetica, sans-serif"><%= patient_name %></font></div></td>
      <td width="6%" bgcolor="#FFFFFF"><div align="center"><font color="#555555" face="Arial, Helvetica, sans-serif"><%= gender %></font></div></td>
      <td width="21%" bgcolor="#FFFFFF"><div align="center"><font color="#555555" face="Arial, Helvetica, sans-serif"><%= birthday %></font></div></td>
      <td width="16%" bgcolor="#FFFFFF"><div align="center"><font face="Arial, Helvetica, sans-serif" color="#555555"><%= q_id %></font></div></td>
      <td width="6%" bgcolor="#FFFFFF"><div align="center"><font face="Arial, Helvetica, sans-serif" color="#555555"><%= score %></font></div></td>
      <td width="11%" bgcolor="#FFFFFF"><div align="center"><font color="#555555" face="Arial, Helvetica, sans-serif"><%= time %></font></div></td>
      <td width="14%" bgcolor="#FFFFFF"><div align="center"><font color="#55555" face="Arial, Helvetica, sans-serif"><a href="javascript:void(0);" onclick="viewQuestionnaire('<%= id %>');">問卷</a>&nbsp;<a href="javascript:void(0);" onclick="viewReport('<%= patient_no %>','<%= q_id %>');">分析</a></font></div></td>
    </tr>
    <tr><td colspan="9" bgcolor="#dddddd"><img src="images/dot.gif" width="1" height="1"></td></tr>
  </table>
</script>
</body>
</html>
