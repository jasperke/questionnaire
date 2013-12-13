// main.js
function initQuestionnaire() {
	$('#questionnaire_name').text(q_id);

	setQuest(0);
	$("#optlist").on("click", "a.btn", function (event) {
		$(this).addClass("active text-danger")
		.find("span").removeClass("glyphicon-unchecked").addClass("glyphicon-check").end()
		.siblings("a.active").removeClass("active").removeClass("text-danger")
		.find("span").removeClass("glyphicon-check").addClass("glyphicon-unchecked");

		var sub_q_no = $(this).data('sub_q_no'),
			q_no = $(this).data('q_no'),
			val = $(this).data('val');
		keepAnswer(q_no, sub_q_no, val); // 暫存答案至answer

		if (event.originalEvent && q_no < quizzes.length - 1) { // user作答完, 自動換下一題
															// 切換上下題時, 標示已填答案也是trigger click event, 但不必換題
			setTimeout(function () {
				setQuest(1);
			}, 300);
		}
	});
}
function keepAnswer(q_no, sub_q_no, val) {
	var old_answer;
	if (sub_q_no == -1) { // 目前處在主問題
		if (answer[q_no] !== undefined) { // 之前已作答過
			old_answer = answer[q_no].toString().split(':'); // ex. '1:0,1,2' 表示主問題選1,子問題群依序選0,1,2
			if (val != old_answer[0]) { // 換答案, 須清掉子問題已填的答案(如果有的話)
				answer[q_no] = val;
			} else { // 未更換答案, 不需任何處理

			}
		} else { // 頭一次作答
			answer[q_no] = val;
		}
	} else { // 目前處在子問題
		old_answer = answer[q_no].toString().split(':');
		sub_answer = (old_answer[1] === undefined || old_answer[1] === '') ? [] : old_answer[1].toString().split(',');
		sub_answer[sub_q_no] = val;
		answer[q_no] = old_answer[0] + ':' + sub_answer.join(',');
	}
}
function setQuest(direction) {
	var quiz_id,
		sub_quizzes,
		curr_quiz_def = quizzes[q_no].toString().split(':'),
		next_quiz_def,
		prev_quiz_def,
		optlistHere;
		// ex. 'X6:X7,_X8,.....,_X22:A1,A2,...:...' 如有冒號, 冒號後表子問題群組, 可多個冒號區隔
		// 第1個冒號後接著的表第1子群問題(主問題選第1項時才秀)
		// 第2個冒號後接著的表第2子群問題(主問題選第2項時才秀)
		// 依此類推
	if (direction > 0) { // next
		if (answer[q_no] === undefined) { // 避免快速亂按, 造成目前題未回答就要往下一題
			alertModal('請確實回答！');
			return;
		}
		if (curr_quiz_def[1] === undefined) { // 當前問題無子問題群
			q_no += direction;
			next_quiz_def = quizzes[q_no].toString().split(':');
			quiz_id = next_quiz_def[0];
		} else { // 主問題某選項下有子問題群
			sub_quizzes = curr_quiz_def[parseInt(answer[q_no], 10) + 1];
			if (sub_quizzes !== undefined) { // 挑的答案選項有子問題
				sub_quizzes = sub_quizzes.toString().split(',');
				sub_q_no++;
				if (sub_quizzes[sub_q_no] !== undefined) { // 後面還有子問題
					quiz_id = sub_quizzes[sub_q_no];
				} else { // 後面已無子問題, 秀下一個主問題
					sub_q_no = -1;
					q_no += direction;
					next_quiz_def = quizzes[q_no].toString().split(':');
					quiz_id = next_quiz_def[0];
				}
			} else { // 挑的答案選項無子問題, 直接秀下一個主問題
				sub_q_no = -1;
				q_no += direction;
				next_quiz_def = quizzes[q_no].toString().split(':');
				quiz_id = next_quiz_def[0];
			}
		}
	} else if (direction < 0) { // previous
		if (sub_q_no > -1) { // 在子問題群內
			sub_q_no--;
			sub_quizzes = curr_quiz_def[parseInt(answer[q_no], 10) + 1].toString().split(',');
			if (sub_q_no >= 0) { // 前面有子問題
				quiz_id = sub_quizzes[sub_q_no];
			} else { // 前已無子問題, 秀主問題
				quiz_id = curr_quiz_def[0];
			}
		} else { // 需檢查前一主問題是否有子問題群
			q_no--;
			prev_quiz_def = quizzes[q_no].toString().split(':');
			if (prev_quiz_def[1] === undefined) { // 無子問題群
				quiz_id = prev_quiz_def[0];
			} else { // 主問題某選項下有子問題群
				sub_quizzes = prev_quiz_def[parseInt(answer[q_no], 10) + 1];
				if (sub_quizzes !== undefined) { // 挑的答案選項有子問題, 秀子問題群中的最後一題
					sub_quizzes = sub_quizzes.toString().split(',');
					sub_q_no = sub_quizzes.length - 1;
					quiz_id = sub_quizzes[sub_q_no];
				} else {
					quiz_id = prev_quiz_def[0];
				}
			}
		}
	} else {
		quiz_id = curr_quiz_def[0];
	}


	if (q_no === 0 && sub_q_no === -1) {
		$("#prevQ").hide();
	} else {
		$("#prevQ").show();
	}
	if (q_no >= quizzes.length - 1) { // TODO: 若最後一題有子問題群, '下一題'會變得有點複雜, 有空再說...
									// 先只考慮是否是最後一大題
		$("#nextQ").hide();
		if (!sent) $("#send").show();
	} else {
		$("#nextQ").show();
		$("#send").hide();
	}

	$("#q_no").html((q_no + 1) + "/" + quizzes.length);
	$('#foreword').html((quizPool[quiz_id].foreword !== undefined) ? '<h4><div class="text-danger">' + quizPool[quiz_id].foreword + '</div></h4>' : '');
	$("#q_title").html(quizPool[quiz_id].quiz);

	optlistHere = $("#optlist");
	optlistHere.empty();

	if (quiz_id == '_SCORE_OF_PAIN') { // 疼痛指數, 特例橫排
		optlistHere.removeClass('btn-group-vertical').addClass('btn-group');
	} else {
		optlistHere.removeClass('btn-group').addClass('btn-group-vertical');
	}

	if (quizPool[quiz_id].image !== undefined) {
		$("#fore_img").append($('<img>', {src: quizPool[quiz_id].image}));
	} else {
		$("#fore_img").empty();
	}

	$.each(quizPool[quiz_id].options || commonOptions, function (i) {
		var $a = $("<a/>").data({q_no: q_no, sub_q_no: sub_q_no, val: i})
				.addClass("btn btn-default").css({whiteSpace: 'normal'})
				.append($("<span/>").addClass("glyphicon glyphicon-unchecked"));
		if (quiz_id != '_SCORE_OF_PAIN') {
			$a.addClass("text-left");
		} else { // 疼痛指數橫排項目太多, checkbox icon後需換行, 居中
			$a.addClass('text-center')
				.css({width: '58px'}).append($("<br/>")).append($("<br/>")).append(' ');
		}
		$a.append(this);
		$("#optlist").append($a);
	});
	if (answer[q_no] !== undefined) {
		if (sub_q_no == -1) {
			$("#optlist").find('a.btn').eq(parseInt(answer[q_no], 10)).trigger('click');
		} else {
			sub_answer = answer[q_no].toString().split(':');
			if (sub_answer[1] !== undefined && sub_answer[1] !== '') {
				sub_answer = sub_answer[1].toString().split(',');
				if (sub_answer[sub_q_no] !== undefined) {
					$("#optlist").find('a.btn').eq(sub_answer[sub_q_no]).trigger('click');
				}
			}
		}
	}
	$(window).scrollTop(0);
}
function saveQuestionnaire(q_name, f, answer, quizzes) {
	$("#send").hide();
	$.ajax({
		url: 'rpc/counter.php',
		dataType: 'json',
		type: 'POST',
		data: {
			questionnaire: q_name,
			p_id: f.p_id.value,
			p_name: f.p_name.value,
			p_weight: f.p_weight.value,
			answer: answer
		},
		error: function (ajaxObj, errorType, exceptionObj) {
			alertModal(errorType + '\n' + exceptionObj);
		},
		success: function (data) {
			if (data[0][0] === 0) {
				$("#prevQ").hide();
				alertModal('謝謝您的合作！', '　');
				sent = true;
			} else {
				alertModal('錯誤代碼：' + data[0][0] + '\n錯誤訊息：' + data[0][1]);
				$("#send").show();
			}
		}
	});
}
function isValidForm(f) {
	for (var i = quizzes.length - 1; i >= 0; i--) {
		if (answer[i] === undefined) {
			alertModal('請確實回答！(第' + (i + 1) + '題)');
			return false;
		}
	}
	saveQuestionnaire(q_id, f, answer, quizzes);
	return false;
}
function alertModal(msg, title) {
	$('#modalTitle').html(title === undefined ? '錯誤訊息！' : title);
	$('#errorMsgHere').html(msg.replace('\n', '<br>'));
	$('#errorModal').modal('show');
}
function startQuest() {
	var f = document.forms[0];
	f.p_id.value = $.trim(f.p_id.value);
	if (f.p_id.value === '' || f.p_weight.value === '') {
		alertModal('資料輸入不完整！');
		return;
	} else if (isNaN(f.p_weight.value) || f.p_weight.value < 5 || f.p_weight.value > 200) {
		alertModal('輸入體重格式不正確！');
		return;
	}

	if (f.p_last_weight.value !== '' && Math.abs(f.p_last_weight.value - f.p_weight.value) > 3) {
		$('#weightOkButton').on('click', function () {
			$('#confirmModal').modal('hide');
			startQuestCore();
		});
		$('#confirmModal').modal('show');
	} else {
		startQuestCore();
	}
}
function startQuestCore() {
	var f = document.forms[0];
	$('#q_no').removeClass('invisible');
	$('#span_p_name').text('病患：' + f.p_name.value);
	$('#span_p_id').text('(' + f.p_id.value + ')');
	$('#door').hide();
	$('#paper').show();
}
function findPatient(no) {
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
			} else { // CreateTime,RandNum,No,Name,Gender,Birthday,Email,Phone,Weight
				var p_name = '',
					_tmp;
				if (data[1] !== undefined) { // 名字第二字替換成○
					_tmp = data[1][3].split('');
					_tmp[1] = '○';
					p_name = _tmp.join('');

					document.forms[0].p_name.value = p_name;
					document.forms[0].p_last_weight.value = data[1][8] || '';
				} else {
					document.forms[0].p_name.value = '';
					document.forms[0].p_last_weight.value = '';
				}
			}
		}
	});
}
function toEra(y, reverse) {
	var dA = y.match(/(\d+)\D+(\d+)\D+(\d+)/);
	if (dA) {
		if (reverse) { // 西元轉民國
			dA[1] = parseInt(dA[1], 10) - 1911;
			return '民國' + dA[1] + '年' + dA[2] + '月' + dA[3] + '日';
		} else { // 民國轉西元
			dA[1] = parseInt(dA[1], 10) + 1911;
			return dA[1] + '-' + dA[2] + '-' + dA[3];
		}
	} else {
		return '';
	}
}