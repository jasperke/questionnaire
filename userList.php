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
<style type="text/css">
<!--
.activeRow {background-color:#FFFFD9;}
table.listTable td {font-size:20px;}
-->
</style>
<script src="js/jquery.min.js" ></script>
<script src="js/underscore-min.js" ></script>
<script src="js/main.min.js" ></script>
</head>

<body bgcolor="#EEEEEE" leftmargin="0" topmargin="0">
<div id="Mask" style="position:absolute; left:0px; top:0px; width:100%; z-index:1; display:none; background-color:rgba(197, 198, 235, 0.8);">&nbsp;</div>
<div id="Layer1" style="position:absolute; left:0px; top:0px; width:100%; z-index:2; display:none;">
<form name='editUserForm' style='margin:40px 0px;'>
<input type="hidden" name="patient_id">

	<table width="870" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#eeeeee" style="padding:1px; border:1px solid #6078B9;"> <!-- bordercolor="#dddddd"  -->
		<tr>
			<td colspan="4" bgcolor="#0B1F80">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr><td width="5%">&nbsp;</td>
						<td width="90%"><div align="center" style="color:#ffffff; font-size:20px;">編輯病患基本資料</div></td>
						<td width="5%"><div align="right" style="color:#ffffff; font-size:20px; font-family: arial; cursor:pointer;" onClick="showEditorLayer(0);">&nbsp;X&nbsp;</div></td></tr>
				</table>
		</tr>
		<tr height="40">
			<td width="100" bgcolor="#CCCCCC"><div align="center">病歷號</div></td>
			<td width="770" colspan="3"><input type="text" name="no" onkeyup="checkPatient(this.value);"></td>
		</tr>
		<tr height="40">
			<td bgcolor="#CCCCCC"><div align="center">姓名</div></td>
			<td><input type="text" name="patient_name"></td>
			<td bgcolor="#CCCCCC"><div align="center">性別</div></td>
			<td><label><input type="radio" name="gender" value="1"> 男</label>&nbsp;&nbsp;
				<label><input type="radio" name="gender" value="2"> 女</label></td>
		</tr>
		<tr height="40">
			<td bgcolor="#CCCCCC"><div align="center">出生年月日</div></td>
			<td><input class="date" name="birthday" type="text" size="24"></td>
			<td bgcolor="#CCCCCC"><div align="center">行動電話</div></td>
			<td><input name="phone" type="text" size="22"></td>
		</tr>
		<tr height="40">
			<td bgcolor="#CCCCCC"><div align="center">email</div></td>
			<td colspan="3"><input name="email" type="text" size="70"></td>
		</tr>
		<tr height="40"><td bgcolor="#CCCCCC"><div align="center">第一次訪談日</div></td>
			<td><input class="date" name="first_date" type="text" size="24"></td>
			<td bgcolor="#CCCCCC"><div align="center">最近一次訪談日</div></td>
			<td><input class="date" name="last_date" type="text" size="24"></td></tr>
		<tr height="40">
			<td bgcolor="#CCCCCC"><div align="center">主要照顧者</div></td>
			<td><label><input type="radio" name="caregiver" value="0">配偶</label>&nbsp;
				<label><input type="radio" name="caregiver" value="1">父母</label>&nbsp;
				<label><input type="radio" name="caregiver" value="2">子女</label>&nbsp;
				<label><input type="radio" name="caregiver" value="3">親友</label>&nbsp;
				<label><input type="radio" name="caregiver" value="4">無</label></td>
			<td bgcolor="#CCCCCC"><div align="center">問卷</div></td>
			<td><label><input type="radio" name="volition" value="0">不需要</label>&nbsp;
				<label><input type="radio" name="volition" value="1">需要</label>&nbsp;
				<label><input type="radio" name="volition" value="2">拒作</label></td></tr>
		<tr height="40">
			<td bgcolor="#CCCCCC"><div align="center">癌別 <img src="images/plus-button.png" style="vertical-align:middle; cursor:pointer;" onclick="cancerFieldBuilder();"></div></td>
			<td colspan="3"><div id="cancer_fields">

<script type="text/template" id="cancer_template">
			<div id="cancer_div_<%= idx %>">
				<img data-rowid="<%= idx %>" src="images/minus-button.png" style="vertical-align:middle; cursor:pointer" onclick="removeCancer(<%= idx %>);">
				<input type="text" name="cancer_code_<%= idx %>" style="width:40px" readOnly>
				<select data-rowid="<%= idx %>" name="cancer_category_<%= idx %>"></select>
				<select data-rowid="<%= idx %>" name="cancer_<%= idx %>"></select>
				<div style="margin:0px 0px 0px 76px; font-size:13px;">開始日：<input class="date" type="text" name="cancerStartDate_<%= idx %>" value="<%= startDate %>" size="24">&nbsp;
				結束日：<input class="date" type="text" name="cancerEndDate_<%= idx %>" value="<%= endDate %>" size="24">
				</div>
			</div>
</script>

				</div>
			</td></tr>
		</tr>
		<tr height="40">
			<td bgcolor="#CCCCCC"><div align="center">備註</div></td>
			<td colspan="3"><textarea name="memo" style="width:100%; height:60px;"></textarea></td>
		</tr>
		<tr height="40">
			<td bgcolor="#CCCCCC"><div align="center">問卷</div></td>
			<td colspan="3"><span id="my_questionnaire"></span></td>
		</tr>
		<tr height="60">
			<td height="40" colspan="4" bgcolor="#ffffff"><div align="center">
					<input name="submitButton" type="button" onClick="saveUser();" value="確定">
					<input name="cancelButton" type="button" onClick="showEditorLayer(0);" value="取消">
				</div></td>
		</tr>
	</table>
</form>
</div>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="137" height="50" bgcolor="#000000">&nbsp;</td>
		<td width="808" bgcolor="#000000">t<font color="#999999" size="4">長庚問卷調查系統
			- 病患列表</font></td>
		<td width="183" bgcolor="#000000"><a href="index.php"><font color="#999999" size="4">主選單</font></a></td>
		<td width="153" bgcolor="#000000"><a href="javascript:void(0);"><font color="#999999" size="4" onclick="showEditor({});">新增</font></a></td>
		<td width="70" bgcolor="#000000"><a href="login.php?logout=1"><font color="#999999" size="4">登出</font></a></td>
	</tr>
	<tr>
		<td id="userListHere" colspan="5">
			<div><form name="filerForm" style="margin:14px;"><font size="5">搜尋：病歷號 <input type="text" name="no_filter" style="width:100px;">&nbsp;&nbsp;姓名 <input type="text" name="name_filter" style="width:100px;">&nbsp;&nbsp;出生日期 <input class="date" type="text" name="birthday_filter" style="width:130px;">&nbsp;<input type="button" name="searchButton" value="搜尋" onclick="filterUser(this.form);"></font></div></form></div>

			<table class="listTable" width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr bgcolor="#CCCCCC">
					<td width="6%" height="40">&nbsp;</td>
					<td width="16%"><div align="center"><font size="4">病歷號</font></div></td>
					<td width="8%" bgcolor="#CCCCCC"> <div align="center">性別</div></td>
					<td width="14%" bgcolor="#CCCCCC"><div align="center">姓名</div></td>
					<td width="16%" bgcolor="#CCCCCC"><div align="center">出生年月日</div></td>
					<td width="16%" bgcolor="#CCCCCC"><div align="center">行動電話</div></td>
					<td width="24%" bgcolor="#CCCCCC"><div align="center">email</div></td>
				</tr>
				<tr bgcolor="#666666">
					<td colspan="7"><img src="images/dot.gif" width="1" height="1"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height="50">
		<td colspan="5"><div align="center" style="padding:4px;"><span id="pageSwitcher"></span></div></td>
	</tr>
</table>
<script type="text/template" id="row_template">
<table class="listTable" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr data-idx="<%= idx %>">
		<td width="6%" height="40" bgcolor="#FFFFFF" align="center"><%= start+idx+1 %>.</td>
		<td width="16%" bgcolor="#FFFFFF" align="center"><font face="Arial, Helvetica, sans-serif"><%= no %></font></div></td>
		<td width="8%" bgcolor="#FFFFFF" align="center"><%= gender?(gender==1?'男':'女'):'' %></td>
		<td width="14%" bgcolor="#FFFFFF" align="center"><%= name %></td>
		<td width="16%" bgcolor="#FFFFFF" align="center"><%= birthday %></td>
		<td width="16%" bgcolor="#FFFFFF" align="center"><%= phone %></td>
		<td width="24%" bgcolor="#FFFFFF" align="center"><%= email %></td></tr>
	<tr><td colspan="7" bgcolor="#dddddd"><img src="images/dot.gif" width="1" height="1"></td></tr>
</table>
</script>
<script>
var start=0,
	page_size=20,
	filterUserNo='',
	filterUserName='',
	filterUserBirthday='',
	row_template,
	cancerField_template,
	user_list,
	user_total_count,
	filterUserTimerId;

function showEditorLayer(show,topPos){
	if(show){
		$('#Mask').css({height:$('body').height()}).show();
		$('#Layer1').css({top:topPos}).show();
	}else{
		$('#Mask').hide();
		$('#Layer1').hide();
	}
}
function filterUser(f){ // TODO: 搜尋患者
	start=0;
	filterUserNo=f.no_filter.value;
	filterUserName=f.name_filter.value;
	filterUserBirthday=f.birthday_filter.value;
	if(filterUserBirthday!=''){
		var dA=filterUserBirthday.match(/(\d+)\D+(\d+)\D+(\d+)/);
		dA[1]=parseInt(dA[1],10)+1911;
		filterUserBirthday=dA[1]+'-'+dA[2]+'-'+dA[3];
	}
	f.searchButton.disabled=true;
	getUsers();
}

function getUsers(){
	$.ajax({
		url:'rpc/getUser.php',
		dataType:'json',
		type:'POST',
		data:{
			start:start,
			size:page_size,
			total_count:1,
			filterNo:filterUserNo,
			filterName:filterUserName,
			filterBirthday:filterUserBirthday
		},
		error:function(){
			alert('error'); // TODO:
			document.filerForm.searchButton.disabled=false;
		},
		success:function(data){
			if(data[0][0]!=0){
				alert('錯誤！\n\n錯誤代碼：'+data[0][0]+'\n錯誤訊息：'+data[0][1]);
			}else{
				// if(data[1][1]!=filterUserNo) // 取回資料採非目前過濾條件者
				// 	return;
				user_total_count=data[1][0];
				user_list=data.slice(2);
				tableBuilder();
				refreshPageSwitcher();
			}
			document.filerForm.searchButton.disabled=false;
		}
	});
}
function tableBuilder(){
	var listHere=$('#userListHere'),
		data=user_list;
	listHere.find('table:gt(0)').remove();
	for(var i=0; i<data.length; i++){
		listHere.append( // CreateTime,RandNum,No,Name,Gender,Birthday,Email,Phone,Weight,Cancer,Volition,Caregiver,FirstDate,LastDate,Memo
			row_template({
				pid: data[i][0]+';'+data[i][1],
				idx: i,
				no: data[i][2],
				name: data[i][3],
				gender: data[i][4],
				birthday: toEra(data[i][5],true).replace(/\D/g,'/').replace(/\/$/,''),
				email: data[i][6],
				phone: data[i][7]
			}));
	}

	$('tr[data-idx]').css({cursor:'pointer'}).on('mouseover',function(){
		$(this).find('td').addClass('activeRow');
	}).on('mouseout',function(){
		$(this).find('td').removeClass('activeRow');
	});
}
function refreshPageSwitcher(){
	var switcher=$('#pageSwitcher').empty();
	if(start>0){
		$('<a>').text('上一頁').css({cursor:'pointer',color:'#0000FF',textDecoration:'underline'}).on('click',function(){
			start-=page_size;
			if(start<0) start=0;
			getUsers();
		}).appendTo(switcher);
	}
	if(start+page_size<user_total_count){
		if(start>0)
			switcher.append(' | ');
		$('<a>').text('下一頁').css({cursor:'pointer',color:'#0000FF',textDecoration:'underline'}).on('click',function(){
			start+=page_size;
			getUsers();
		}).appendTo(switcher);
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
function showEditor(opt,topPos){
	cancerFieldBuilder.counter=0;

	var f=document.editUserForm,
			my_questionnaire=$('#my_questionnaire');
	if(opt.patient_id){
		f.patient_id.value=opt.patient_id;
	}else{
		f.patient_id.value='';
	}
	f.no.value=opt.no||'';
	f.patient_name.value=opt.patient_name||'';
	if(opt.gender){
		if(opt.gender==2)
			f.gender[1].checked=true;
		else
			f.gender[0].checked=true;
	}else{
		$(f.gender).each(function(){
			this.checked=false;
		})
	}
	f.birthday.value=toEra(opt.birthday||'',true);
	f.phone.value=opt.phone||'';
	f.email.value=opt.email||'';
	f.first_date.value=toEra(opt.first_date||'',true);
	f.last_date.value=toEra(opt.last_data||'',true);
	if(opt.caregiver!==undefined&&opt.caregiver!==''){
		f.caregiver[opt.caregiver].checked=true;
	}else{
		$(f.caregiver).each(function(){
			this.checked=false;
		})
	}
	if(opt.volition!==undefined&&opt.volition!==''){
		f.volition[opt.volition].checked=true;
	}else{
		$(f.volition).each(function(){
			this.checked=false;
		})
	}
	f.memo.value=opt.memo||'';

	my_questionnaire.empty();
	if(opt.questionnaire!==undefined&&opt.questionnaire.length){
		$(opt.questionnaire).each(function(idx){
			my_questionnaire.append((idx>0?'、':'')+'<a href="report.php?pid='+opt.no+'&quest='+this+'" target="_blank">'+this+'</a>');
		})
	}else{
		my_questionnaire.html('<font color="#666666"> -- 無 --</font>');
	}

	$('#cancer_fields').empty();
	if(opt.cancer&&opt.cancer.length){
		$(opt.cancer).each(function(){
			cancerFieldBuilder({
				val: this[0],
				startDate: this[1],
				endDate: this[2]
			})
		});
	}else{
		cancerFieldBuilder();
	}

	showEditorLayer(1,topPos||0);
}
function saveUser(){
	var f=document.editUserForm,
		data={no:f.no.value,
			patient_id:f.patient_id.value,
			patient_name:f.patient_name.value,
			email:f.email.value,
			phone:f.phone.value,
			first_date:toEra(f.first_date.value),
			last_date:toEra(f.last_date.value),
			memo:f.memo.value
		},
		dA;
	if(f.gender[0].checked)
		data.gender=1;
	if(f.gender[1].checked)
		data.gender=2;

	if(f.birthday.value)
		data.birthday=toEra(f.birthday.value);

	$(f.caregiver).each(function(){
		if(this.checked) data.caregiver=this.value;
	});
	$(f.volition).each(function(){
		if(this.checked) data.volition=this.value;
	})

	data.cancer=[];
	$('input[name^="cancer_code_"]').each(function(){
		var f=document.editUserForm,
				rowid=this.name.replace('cancer_code_','');
		if(f['cancer_code_'+rowid].value)
			data.cancer.push([f['cancer_code_'+rowid].value,f['cancerStartDate_'+rowid].value,f['cancerEndDate_'+rowid].value]);
	})

	$.ajax({
		url: 'rpc/editUser.php',
		dataType: 'json',
		type: 'POST',
		data: data,
		error: function (ajaxObj, errorType, exceptionObj) {
			alert(errorType + '\n' + exceptionObj);
		},
		success: function (data) {
			if (data[0][0] === 0) {
				alert('資料儲存完畢！');

				//start=0;
				getUsers();
			} else {
				alert('錯誤代碼：' + data[0][0] + '\n錯誤訊息：' + data[0][1]);
			}
			showEditorLayer(0);
		}
	});
}
function checkPatient(no) {
	$.ajax({
		url: 'rpc/getUser.php',
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
					showEditor({ // CreateTime,RandNum,No,Name,Gender,Birthday,Email,Phone,Weight,Cancer,Volition,Caregiver,FirstDate,LastDate,Memo
								patient_id:data[1][0]+';'+data[1][1],
								no:data[1][2],
								patient_name:data[1][3],
								gender:data[1][4],
								birthday:data[1][5],
								phone:data[1][7],
								email:data[1][6],
								cancer:data[1][9],
								first_date:data[1][12],
								last_data:data[1][13],
								caregiver:data[1][11],
								volition:data[1][10],
								memo:data[1][14]
							});
				}else{
					document.editUserForm.patient_id.value='';
				}
			}
		}
	});
}
function refreshCancerSubSelector(event){
	var idx=$(event.target).data('rowid'),
			cancerList=cancerTaxonomy.cancerList(document.editUserForm['cancer_category_'+idx].selectedIndex),
			i;
	for(i=document.editUserForm['cancer_'+idx].length-1; i>=0; i--){
		document.editUserForm['cancer_'+idx].options[i]=null;
	}
	for(i=0; i<cancerList.length; i++){
		document.editUserForm['cancer_'+idx].appendChild(new Option(cancerList[i][1],cancerList[i][0]));
	}
	$(document.editUserForm['cancer_'+idx]).trigger('change');
}

function cancerFieldBuilder(cancer){
	var i=0,
			cancerCategory=cancerTaxonomy.categoryList(),
			selectedIndex;

	$('#cancer_fields').append(cancerField_template($.extend({idx:cancerFieldBuilder.rowid}, cancer||{
		val: '',
		startDate: '',
		endDate: ''
	})));
	// 癌別開始/結束日
	$('#cancer_div_'+cancerFieldBuilder.rowid+' .date').datePicker({
		weekName:['日','一','二','三','四','五','六'],
		monthName:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
		taiwan:true
	});

	for(i=0; i<cancerCategory.length; i++){
		document.editUserForm['cancer_category_'+cancerFieldBuilder.rowid].appendChild(new Option(cancerCategory[i],i));
	}

	$(document.editUserForm['cancer_category_'+cancerFieldBuilder.rowid]).on('change',refreshCancerSubSelector).trigger('change');
	$(document.editUserForm['cancer_'+cancerFieldBuilder.rowid]).on('change',function(event){
		var idx=$(event.target).data('rowid');
		document.editUserForm['cancer_code_'+idx].value=this.options[this.selectedIndex].value;
	});

	if(cancer!==undefined){
		selectedIndex=cancerTaxonomy.categoryIdx(cancer.val);
		document.editUserForm['cancer_category_'+cancerFieldBuilder.rowid].selectedIndex=selectedIndex;
		$(document.editUserForm['cancer_category_'+cancerFieldBuilder.rowid]).trigger('change');
		$(document.editUserForm['cancer_'+cancerFieldBuilder.rowid]).val(cancer.val).trigger('change');
	}

	cancerFieldBuilder.rowid++;
	cancerFieldBuilder.counter++;
}
cancerFieldBuilder.rowid=0;
cancerFieldBuilder.counter=0;

function removeCancer(rowid){
	$('div#cancer_div_'+rowid).remove();
	cancerFieldBuilder.counter--;
	if(cancerFieldBuilder.counter==0){ // 至少留一組空白欄
		cancerFieldBuilder();
	}
}

$(function(){
	// 生日/訪談日
	$('.date').datePicker({
		weekName:['日','一','二','三','四','五','六'],
		monthName:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
		taiwan:true
	});

	$('#userListHere').on('click','tr',function(event){
		var i=$(this).data('idx'),
				user={ // CreateTime,RandNum,No,Name,Gender,Birthday,Email,Phone,Weight,Cancer,Volition,Caregiver,FirstDate,LastDate,Memo
					patient_id:user_list[i][0]+';'+user_list[i][1],
					no:user_list[i][2],
					patient_name:user_list[i][3],
					gender:user_list[i][4],
					birthday:user_list[i][5],
					phone:user_list[i][7],
					email:user_list[i][6],
					first_date:user_list[i][12],
					last_data:user_list[i][13],
					memo:user_list[i][14],
					cancer:user_list[i][9],
					questionnaire:user_list[i][15]
				}
		if(user_list[i][11]!==undefined&&user_list[i][11]!=='')
			user.caregiver=user_list[i][11];
		if(user_list[i][10]!==undefined&&user_list[i][10]!=='')
			user.volition=user_list[i][10];

		//$(window).scrollTop(0);
		showEditor(user, $(window).scrollTop());
	});

	row_template=_.template($("#row_template").html());

	cancerField_template=_.template($('#cancer_template').html());
	cancerFieldBuilder();
});
</script>
</body>
</html>
