require.config({
	shim: {
		'jquery':{exports:'$'},
		'underscore':{exports:'_'},
		"bootstrap": ["jquery"]
	}
});

/*require(['domReady!', 'fillOutQuestionnaire', 'jquery', 'underscore', 'bootstrap'], function (doc, fillOut, $, _) {
	var url_params = {};
	$.each(location.search.substr(1).split('&'), function () {
		var p = this.split('=');
		url_params[p[0]] = p[1];
	});
console.log(typeof $);
console.log('main.js:'+new Date().getTime());
	fillOut.q_id = url_params.questionnaire;
alert(fillOut.q_id);
	$.getJSON('questionnaireMap.php', {q_id: q_id}, function (data) {
		fillOut.quizzes = data;
		fillOut.initQuestionnaire();
	});
});*/

require(['jquery','fillOutQuestionnaire'], function ($, fO) {
	var url_params = {};
	$.each(location.search.substr(1).split('&'), function () {
		var p = this.split('=');
		url_params[p[0]] = p[1];
	});
alert('fO');
console.log(typeof fO);
console.log('main.js:'+new Date().getTime());
});