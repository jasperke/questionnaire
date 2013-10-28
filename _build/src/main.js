require.config({
	shim: {
		'jquery':{exports:'$'},
		'underscore':{exports:'_'},
		"bootstrap": ["jquery"]
	}
});

require(['domReady!', 'global', 'fillOutQuestionnaire', 'jquery', 'bootstrap'], function (doc, global, fillOut, $) {
	var url_params = {};
	$.each(location.search.substr(1).split('&'), function () {
		var p = this.split('=');
		url_params[p[0]] = p[1];
	});
	global.q_id = url_params.questionnaire;

	$('#startQ').on('click',fillOut.startQuest);
	$('#prevQ').on('click',function(){fillOut.setQuest(-1);});
	$('#nextQ').on('click',function(){fillOut.setQuest(1);});

	$.getJSON('questionnaireMap.php', {q_id: global.q_id}, function (data) {
		global.quizzes = data;
		global.q_no = 0;
		global.sub_q_no= -1;
		global.answer = [];

		fillOut.initQuestionnaire();
	});
});

// require(['jquery', 'fillOutQuestionnaire'], function ($, fO) {
// 	var url_params = {};
// 	$.each(location.search.substr(1).split('&'), function () {
// 		var p = this.split('=');
// 		url_params[p[0]] = p[1];
// 	});
// console.log('main.js:'+new Date().getTime());
// });