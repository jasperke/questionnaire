<?php
session_start();

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
<script src="js/jquery.min.js" ></script>
<script src="js/underscore-min.js" ></script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; }
}

function MM_dragLayer(objName,x,hL,hT,hW,hH,toFront,dropBack,cU,cD,cL,cR,targL,targT,tol,dropJS,et,dragJS) { //v4.01
  //Copyright 1998 Macromedia, Inc. All rights reserved.
  var i,j,aLayer,retVal,curDrag=null,curLeft,curTop,IE=document.all,NS4=document.layers;
  var NS6=(!IE&&document.getElementById), NS=(NS4||NS6); if (!IE && !NS) return false;
  retVal = true; if(IE && event) event.returnValue = true;
  if (MM_dragLayer.arguments.length > 1) {
    curDrag = MM_findObj(objName); if (!curDrag) return false;
    if (!document.allLayers) { document.allLayers = new Array();
      with (document) if (NS4) { for (i=0; i<layers.length; i++) allLayers[i]=layers[i];
        for (i=0; i<allLayers.length; i++) if (allLayers[i].document && allLayers[i].document.layers)
          with (allLayers[i].document) for (j=0; j<layers.length; j++) allLayers[allLayers.length]=layers[j];
      } else {
        if (NS6) { var spns = getElementsByTagName("span"); var all = getElementsByTagName("div");
          for (i=0;i<spns.length;i++) if (spns[i].style&&spns[i].style.position) allLayers[allLayers.length]=spns[i];}
        for (i=0;i<all.length;i++) if (all[i].style&&all[i].style.position) allLayers[allLayers.length]=all[i];
    } }
    curDrag.MM_dragOk=true; curDrag.MM_targL=targL; curDrag.MM_targT=targT;
    curDrag.MM_tol=Math.pow(tol,2); curDrag.MM_hLeft=hL; curDrag.MM_hTop=hT;
    curDrag.MM_hWidth=hW; curDrag.MM_hHeight=hH; curDrag.MM_toFront=toFront;
    curDrag.MM_dropBack=dropBack; curDrag.MM_dropJS=dropJS;
    curDrag.MM_everyTime=et; curDrag.MM_dragJS=dragJS;
    curDrag.MM_oldZ = (NS4)?curDrag.zIndex:curDrag.style.zIndex;
    curLeft= (NS4)?curDrag.left:(NS6)?parseInt(curDrag.style.left):curDrag.style.pixelLeft;
    if (String(curLeft)=="NaN") curLeft=0; curDrag.MM_startL = curLeft;
    curTop = (NS4)?curDrag.top:(NS6)?parseInt(curDrag.style.top):curDrag.style.pixelTop;
    if (String(curTop)=="NaN") curTop=0; curDrag.MM_startT = curTop;
    curDrag.MM_bL=(cL<0)?null:curLeft-cL; curDrag.MM_bT=(cU<0)?null:curTop-cU;
    curDrag.MM_bR=(cR<0)?null:curLeft+cR; curDrag.MM_bB=(cD<0)?null:curTop+cD;
    curDrag.MM_LEFTRIGHT=0; curDrag.MM_UPDOWN=0; curDrag.MM_SNAPPED=false; //use in your JS!
    document.onmousedown = MM_dragLayer; document.onmouseup = MM_dragLayer;
    if (NS) document.captureEvents(Event.MOUSEDOWN|Event.MOUSEUP);
  } else {
    var theEvent = ((NS)?objName.type:event.type);
    if (theEvent == 'mousedown') {
      var mouseX = (NS)?objName.pageX : event.clientX + document.body.scrollLeft;
      var mouseY = (NS)?objName.pageY : event.clientY + document.body.scrollTop;
      var maxDragZ=null; document.MM_maxZ = 0;
      for (i=0; i<document.allLayers.length; i++) { aLayer = document.allLayers[i];
        var aLayerZ = (NS4)?aLayer.zIndex:parseInt(aLayer.style.zIndex);
        if (aLayerZ > document.MM_maxZ) document.MM_maxZ = aLayerZ;
        var isVisible = (((NS4)?aLayer.visibility:aLayer.style.visibility).indexOf('hid') == -1);
        if (aLayer.MM_dragOk != null && isVisible) with (aLayer) {
          var parentL=0; var parentT=0;
          if (NS6) { parentLayer = aLayer.parentNode;
            while (parentLayer != null && parentLayer.style.position) {
              parentL += parseInt(parentLayer.offsetLeft); parentT += parseInt(parentLayer.offsetTop);
              parentLayer = parentLayer.parentNode;
          } } else if (IE) { parentLayer = aLayer.parentElement;
            while (parentLayer != null && parentLayer.style.position) {
              parentL += parentLayer.offsetLeft; parentT += parentLayer.offsetTop;
              parentLayer = parentLayer.parentElement; } }
          var tmpX=mouseX-(((NS4)?pageX:((NS6)?parseInt(style.left):style.pixelLeft)+parentL)+MM_hLeft);
          var tmpY=mouseY-(((NS4)?pageY:((NS6)?parseInt(style.top):style.pixelTop) +parentT)+MM_hTop);
          if (String(tmpX)=="NaN") tmpX=0; if (String(tmpY)=="NaN") tmpY=0;
          var tmpW = MM_hWidth;  if (tmpW <= 0) tmpW += ((NS4)?clip.width :offsetWidth);
          var tmpH = MM_hHeight; if (tmpH <= 0) tmpH += ((NS4)?clip.height:offsetHeight);
          if ((0 <= tmpX && tmpX < tmpW && 0 <= tmpY && tmpY < tmpH) && (maxDragZ == null
              || maxDragZ <= aLayerZ)) { curDrag = aLayer; maxDragZ = aLayerZ; } } }
      if (curDrag) {
        document.onmousemove = MM_dragLayer; if (NS4) document.captureEvents(Event.MOUSEMOVE);
        curLeft = (NS4)?curDrag.left:(NS6)?parseInt(curDrag.style.left):curDrag.style.pixelLeft;
        curTop = (NS4)?curDrag.top:(NS6)?parseInt(curDrag.style.top):curDrag.style.pixelTop;
        if (String(curLeft)=="NaN") curLeft=0; if (String(curTop)=="NaN") curTop=0;
        MM_oldX = mouseX - curLeft; MM_oldY = mouseY - curTop;
        document.MM_curDrag = curDrag;  curDrag.MM_SNAPPED=false;
        if(curDrag.MM_toFront) {
          eval('curDrag.'+((NS4)?'':'style.')+'zIndex=document.MM_maxZ+1');
          if (!curDrag.MM_dropBack) document.MM_maxZ++; }
        retVal = false; if(!NS4&&!NS6) event.returnValue = false;
    } } else if (theEvent == 'mousemove') {
      if (document.MM_curDrag) with (document.MM_curDrag) {
        var mouseX = (NS)?objName.pageX : event.clientX + document.body.scrollLeft;
        var mouseY = (NS)?objName.pageY : event.clientY + document.body.scrollTop;
        newLeft = mouseX-MM_oldX; newTop  = mouseY-MM_oldY;
        if (MM_bL!=null) newLeft = Math.max(newLeft,MM_bL);
        if (MM_bR!=null) newLeft = Math.min(newLeft,MM_bR);
        if (MM_bT!=null) newTop  = Math.max(newTop ,MM_bT);
        if (MM_bB!=null) newTop  = Math.min(newTop ,MM_bB);
        MM_LEFTRIGHT = newLeft-MM_startL; MM_UPDOWN = newTop-MM_startT;
        if (NS4) {left = newLeft; top = newTop;}
        else if (NS6){style.left = newLeft; style.top = newTop;}
        else {style.pixelLeft = newLeft; style.pixelTop = newTop;}
        if (MM_dragJS) eval(MM_dragJS);
        retVal = false; if(!NS) event.returnValue = false;
    } } else if (theEvent == 'mouseup') {
      document.onmousemove = null;
      if (NS) document.releaseEvents(Event.MOUSEMOVE);
      if (NS) document.captureEvents(Event.MOUSEDOWN); //for mac NS
      if (document.MM_curDrag) with (document.MM_curDrag) {
        if (typeof MM_targL =='number' && typeof MM_targT == 'number' &&
            (Math.pow(MM_targL-((NS4)?left:(NS6)?parseInt(style.left):style.pixelLeft),2)+
             Math.pow(MM_targT-((NS4)?top:(NS6)?parseInt(style.top):style.pixelTop),2))<=MM_tol) {
          if (NS4) {left = MM_targL; top = MM_targT;}
          else if (NS6) {style.left = MM_targL; style.top = MM_targT;}
          else {style.pixelLeft = MM_targL; style.pixelTop = MM_targT;}
          MM_SNAPPED = true; MM_LEFTRIGHT = MM_startL-MM_targL; MM_UPDOWN = MM_startT-MM_targT; }
        if (MM_everyTime || MM_SNAPPED) eval(MM_dropJS);
        if(MM_dropBack) {if (NS4) zIndex = MM_oldZ; else style.zIndex = MM_oldZ;}
        retVal = false; if(!NS) event.returnValue = false; }
      document.MM_curDrag = null;
    }
    if (NS) document.routeEvent(objName);
  } return retVal;
}
//-->
</script>
</head>

<body bgcolor="#EEEEEE" leftmargin="0" topmargin="0">
<div id="Layer1" style="position:absolute; left:0px; top:100px; width:100%; z-index:1; visibility: hidden;">
<form name='editUserForm' style='margin:0px;'>
<input type="hidden" name="patient_id">
  <table width="750" border="1" align="center" cellpadding="1" cellspacing="0" bordercolor="#dddddd" bgcolor="#EEEEEE">
    <tr>
      <td height="55" colspan="4">
        <div align="center"><font size="6"><font color="#555555">編輯病患基本資料</font></font></div></td>
    </tr>
    <tr>
      <td width="146" height="40" bgcolor="#CCCCCC"><div align="center">病例號</div></td>
      <td width="252"><input type="text" name="no" onkeyup="checkPatient(this.value);"></td>
      <td width="146" bgcolor="#CCCCCC"><div align="center">身分證字號</div></td>
      <td width="246"><input name="id_no" type="text" size="30" maxlength="10"></td>
    </tr>
    <tr>
      <td height="40" bgcolor="#CCCCCC"><div align="center">姓名</div></td>
      <td><input type="text" name="patient_name"></td>
      <td bgcolor="#CCCCCC"><div align="center">性別</div></td>
      <td><label><input type="radio" name="gender" value="1"> 男</label>&nbsp;&nbsp;
        <label><input type="radio" name="gender" value="2"> 女</label></td>
    </tr>
    <tr>
      <td height="40" bgcolor="#CCCCCC"><div align="center">出生年月日</div></td>
      <td>西元
        <input name="birthday_yy" type="text" size="6" maxlength="4"> 年
        <input name="birthday_mm" type="text" size="2" maxlength="2"> 月
        <input name="birthday_dd" type="text" size="2" maxlength="2"> 日</td>
      <td bgcolor="#CCCCCC"><div align="center">行動電話</div></td>
      <td><input name="phone" type="text" size="22"></td>
    </tr>
    <tr>
      <td height="40" bgcolor="#CCCCCC"><div align="center">email</div></td>
      <td colspan="3"><input name="email" type="text" size="50"> <div align="center"></div></td>
    </tr>
    <tr>
      <td height="50" colspan="4">
        <div align="center">
          <input name="submitButton" type="button" onClick="saveUser();" value="確定">
          <input name="cancelButton" type="button" onClick="MM_showHideLayers('Layer1','','hide')" value="取消">
        </div></td>
    </tr>
  </table>
</form>
</div>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="137" height="50" bgcolor="#000000">&nbsp;</td>
    <td width="908" bgcolor="#000000">t<font color="#999999" size="4">長庚問卷調查系統
      - 病患列表</font></td>
    <td width="83" bgcolor="#000000"><a href="index.php"><font color="#999999" size="4">回上頁</font></a></td>
    <td width="153" bgcolor="#000000"><a href="#"><font color="#999999" size="4" onclick="showEditor({});">新增</font></a></td>
    <td width="70" bgcolor="#000000"><a href="login.php?logout=1"><font color="#999999" size="4">登出</font></a></td>
  </tr>
  <tr>
    <td id="userListHere" colspan="5"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr bgcolor="#CCCCCC">
          <td width="14%" height="30"> <div align="center"><font size="4">病例號</font></div></td>
          <td width="14%"> <div align="center">身分證字號</div></td>
          <td width="9%" bgcolor="#CCCCCC"> <div align="center">性別</div></td>
          <td width="12%" bgcolor="#CCCCCC"><div align="center">姓名</div></td>
          <td width="12%" bgcolor="#CCCCCC"><div align="center">出生年月日</div></td>
          <td width="10%" bgcolor="#CCCCCC"><div align="center">行動電話</div></td>
          <td width="20%" bgcolor="#CCCCCC"><div align="center">email</div></td>
          <td width="9%"> <div align="center"><font size="4">功能</font></div></td>
        </tr>
        <tr bgcolor="#666666">
          <td colspan="8"><img src="images/dot.gif" width="1" height="1"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="5">&nbsp;</td>
  </tr>
</table>
<script type="text/template" id="row_template">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr><td width="14%" height="30" bgcolor="#FFFFFF"> <div align="center"><font color="#555555" face="Arial, Helvetica, sans-serif"><%= no %></font></div></td>
    <td width="14%" bgcolor="#FFFFFF" align="center"><%= id %></td>
    <td width="9%" bgcolor="#FFFFFF" align="center"><%= gender?(gender==1?'男':'女'):'' %></td>
    <td width="12%" bgcolor="#FFFFFF" align="center"><%= name %></td>
    <td width="12%" bgcolor="#FFFFFF" align="center"><%= birthday %></td>
    <td width="10%" bgcolor="#FFFFFF" align="center"><%= phone %></td>
    <td width="20%" bgcolor="#FFFFFF" align="center"><%= email %></td>
    <td width="9%" bgcolor="#FFFFFF" align="center"><a data-idx="<%= idx %>" style="cursor:pointer;">編輯</a></td></tr>
  <tr><td colspan="8" bgcolor="#dddddd"><img src="images/dot.gif" width="1" height="1"></td></tr>
</table>
</script>
<script>
var start=0,
  page_size=20,
  row_template,
  user_list;

function getUsers(){
  $.ajax({
    url:'getUser.php',
    dataType:'json',
    type:'POST',
    data:{
      start:start,
      size:page_size
    },
    error:function(){
      alert('error'); // TODO:
    },
    success:function(data){
      if(data[0][0]!=0){
        alert('錯誤！\n\n錯誤代碼：'+data[0][0]+'\n錯誤訊息：'+data[0][1]);
      }else{
        user_list=data.slice(1);
        tableBuilder();
      }
    }
  });
}
function tableBuilder(){
  var listHere=$('#userListHere'),
    data=user_list;
  listHere.find('table:gt(0)').remove();
  for(var i=0; i<data.length; i++){
    listHere.append( // CreateTime,RandNum,No,Id,Name,Gender,Birthday,Email,Phone
      row_template({
        pid: data[i][0]+';'+data[i][1],
        idx: i,
        no: data[i][2],
        id: data[i][3],
        name: data[i][4],
        gender: data[i][5],
        birthday: data[i][6],
        email: data[i][7],
        phone: data[i][8]
      }));
  }
}
function isValidDate(yy,mm,dd){
  if(isNaN(yy)||isNaN(mm)||isNaN(dd)){return false;}
  if(yy<=0||mm<=0||mm>12||dd<=0||dd>31){return false;}
  if((mm==4||mm==6||mm==9||mm==11)&&dd>30){return false;}
  if(mm==2){
    if((yy%4==0&&yy%100!=0)||yy%1000==0){
      if(dd>29){return false;}
    }else{
      if(dd>28){return false;}
    }
  }
  return true;
};
function showEditor(opt){
  var f=document.editUserForm;
  if(opt.patient_id){
    f.patient_id.value=opt.patient_id;
//    $(f.no).attr('readOnly','readOnly');
  }else{
    f.patient_id.value='';
//    $(f.no).removeAttr('readOnly');
  }
  f.no.value=opt.no||'';
  f.id_no.value=opt.id_no||'';
  f.patient_name.value=opt.patient_name||'';
  if(opt.gender&&opt.gender==2)
    f.gender[1].checked=true;
  else
    f.gender[0].checked=true;
  if(opt.birthday){
    var birth=opt.birthday.split('-');
    f.birthday_yy.value=birth[0];
    f.birthday_mm.value=birth[1];
    f.birthday_dd.value=birth[2];
  }else{
    f.birthday_yy.value='';
    f.birthday_mm.value='';
    f.birthday_dd.value='';
  }
  f.phone.value=opt.phone||'';
  f.email.value=opt.email||'';

  MM_showHideLayers('Layer1','','show')
}
function saveUser(){
  var f=document.editUserForm,
    data={no:f.no.value,
      gender:f.gender[0].checked?1:2,
      patient_id:f.patient_id.value,
      id_no:f.id_no.value,
      patient_name:f.patient_name.value,
      email:f.email.value,
      phone:f.phone.value
    };
  if(isValidDate(f.birthday_yy.value,f.birthday_mm.value,f.birthday_dd.value))
    data.birthday=f.birthday_yy.value+'-'+f.birthday_mm.value+'-'+f.birthday_dd.value;

  $.ajax({
    url: 'editUser.php',
    dataType: 'json',
    type: 'POST',
    data: data,
    error: function (ajaxObj, errorType, exceptionObj) {
      alert(errorType + '\n' + exceptionObj);
    },
    success: function (data) {
      if (data[0][0] === 0) {
        alert('資料儲存完畢！');

        start=0;
        getUsers();
      } else {
        alert('錯誤代碼：' + data[0][0] + '\n錯誤訊息：' + data[0][1]);
      }
      MM_showHideLayers('Layer1','','hide');
    }
  });
}
function checkPatient(no) {
  $.ajax({
    url: 'getUser.php',
    dataType: 'json',
    type: 'POST',
    data: {no: no},
    error: function () {
      alert('error'); // TODO:
    },
    success: function (data) {
      if (data[0][0] !== 0) {
        alert('錯誤！\n\n錯誤代碼：' + data[0][0] + '\n錯誤訊息：' + data[0][1]);
      } else {
        if(data.length>1){
          showEditor({ // CreateTime,RandNum,No,Id,Name,Gender,Birthday,Email,Phone
                patient_id:data[1][0]+';'+data[1][1],
                no:data[1][2],
                id_no:data[1][3],
                patient_name:data[1][4],
                gender:data[1][5],
                birthday:data[1][6],
                phone:data[1][8],
                email:data[1][7]
              });
        }else{
          document.editUserForm.patient_id.value='';
        }
      }
    }
  });
}
$(function(){
  $('#userListHere').on('click','a',function(event){
    var i=$(this).data('idx');
    showEditor({ // CreateTime,RandNum,No,Id,Name,Gender,Birthday,Email,Phone
      patient_id:user_list[i][0]+';'+user_list[i][1],
      no:user_list[i][2],
      id_no:user_list[i][3],
      patient_name:user_list[i][4],
      gender:user_list[i][5],
      birthday:user_list[i][6],
      phone:user_list[i][8],
      email:user_list[i][7]
    });
  });
  row_template=_.template($("#row_template").html());
  getUsers();
})
</script>
</body>
</html>
