'use strict';

module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		BASE_PATH: '../',
		LIB_PATH: './bower_components/',
		JS_PATH: '../js/',
		TMP_PATH: '../js/',
		SRC_PATH: './src/',

		banner: [
				 '/*',
				 '* Project: <%= pkg.name %>',
				 '* Version: <%= pkg.version %> (<%= grunt.template.today("yyyy-mm-dd HH:MM") %>)',
				 '* Development By: <%= pkg.author %>',
				 '* Copyright(c): <%= grunt.template.today("yyyy") %>',
				 '*/',
				 ''
		],

		concat: {
			dist: {
				src: ['./src/main.js','./src/quizPool.js'],
				dest: '<%= TMP_PATH %>'+'main.js'
			},
			dev: {
				src: ['./src/main.js','./src/quizPool.js'],
				dest: '<%= JS_PATH %>'+'main.min.js'
			}
		},
		clean: {
			dist: ['<%= TMP_PATH %>']
		},
		uglify: {
			options: {
				//banner: '<%= banner.join("\\n") %>'
				mangle:false
			},
			dist: {
				src: '<%= TMP_PATH %>'+'**/*.js',
				dest: '<%= JS_PATH %>',
				expand: true, 			// 須用expand:true, allow dynamic building
				flatten: true 			// 移除原本在tmp/內的目錄結構, 全部直接產生在/js/目錄下
			}
		},
		copy: {
			dist: {
				files: [
					{expand:true, cwd:'<%= LIB_PATH %>'+'underscore', src:'underscore.js', dest:'<%= TMP_PATH %>'},
					{expand:true, cwd:'<%= LIB_PATH %>'+'jquery', src:'jquery.js', dest:'<%= TMP_PATH %>'},
					{expand:true, cwd:'<%= LIB_PATH %>'+'bootstrap/dist', src:'**', dest:'<%= BASE_PATH %>'},
					{expand:true, cwd:'<%= LIB_PATH %>'+'requirejs-domready', src:'domReady.js', dest:'<%= TMP_PATH %>'},
					{expand:true, cwd:'<%= LIB_PATH %>'+'requirejs', src:'require.js', dest:'<%= JS_PATH %>'},
					{expand:true, cwd: './src', src:'*.js', dest:'<%= TMP_PATH %>'}
				]
			}
		},
		watch: {
			scripts: {
				files: ['./src/*.js'],
				tasks: ['development'],
				options: {
					spawn: true,
				}
		  	}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('lib', ['copy']);
	grunt.registerTask('bower', function() { // 試不出來??
		grunt.util.spawn({
			cmd: ['bower'],
			args: ['install'],
		}, function done() {
			grunt.log.writeln('down bower');
		});
	});

	grunt.registerTask('product', ['concat:dist','uglify','clean']);
	grunt.registerTask('development', ['concat:dev']);

	grunt.registerTask('try',['copy']);
	grunt.registerTask('default', ['lib','product']);
};