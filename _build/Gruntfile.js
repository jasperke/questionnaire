module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		BASE_PATH: '../',
		LIB_PATH: './bower_components/',
		JS_PATH: '../js/',

		banner: [
				 '/*',
				 '* Project: <%= pkg.name %>',
				 '* Version: <%= pkg.version %> (<%= grunt.template.today("yyyy-mm-dd") %>)',
				 '* Development By: <%= pkg.author %>',
				 '* Copyright(c): <%= grunt.template.today("yyyy") %>',
				 '*/'
		],
		uglify: {
			options: {
				banner: '<%= banner.join("\\n") %>'
			},
			dist: {
				src: ['../src/quizPool.js'],
				dest: '<%= JS_PATH %>'+'quizPool.min.js'
			}
		},
		copy: {
			dist: {
				files: [
					{expand:true, cwd:'<%= LIB_PATH %>'+'underscore', src:'underscore-min.js', dest:'<%= JS_PATH %>'},
					{expand:true, cwd:'<%= LIB_PATH %>'+'jquery', src:'jquery.min.js', dest:'<%= JS_PATH %>'},
					{expand:true, cwd:'<%= LIB_PATH %>'+'bootstrap/dist', src:'**', dest:'<%= BASE_PATH %>'}
				]
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-uglify');

	grunt.registerTask('default', ['uglify','copy']);
};