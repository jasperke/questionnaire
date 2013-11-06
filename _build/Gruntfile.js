'use strict';

module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		BASE_PATH: '../',
		LIB_PATH: './bower_components/',
		JS_PATH: '../js/',

		banner: [
				 '/*',
				 '* Project: <%= pkg.name %>',
				 '* Version: <%= pkg.version %> (<%= grunt.template.today("yyyy-mm-dd HH:MM") %>)',
				 '* Development By: <%= pkg.author %>',
				 '* Copyright(c): <%= grunt.template.today("yyyy") %>',
				 '*/',
				 ''
		],

		// concat: {
		// 	dist: {
		// 		src: ['./src/main.js','./src/quizPool.js'],
		// 		dest: './tmp/main.js'
		// 	},
		// 	dev: {
		// 		src: ['./src/main.js','./src/quizPool.js'],
		// 		dest: '<%= JS_PATH %>main.min.js'
		// 	}
		// },
		// clean: {
		// 	dist: ['./tmp']
		// },
		uglify: {
			options: {
				banner: '<%= banner.join("\\n") %>',
				beautify : {ascii_only: true},
				mangle: false,
				sourceMap: '<%= JS_PATH %>main.min.map',
				sourceMapRoot: '<%= JS_PATH %>',
				sourceMapPrefix: 2,
				sourceMappingURL: 'main.min.map'
			},
			dist: {
				files: {
					'<%= JS_PATH %>main.min.js': ['<%= JS_PATH %>src/quizPool.js','<%= JS_PATH %>src/main.js']
				}
			}
		},
		copy: {
			dist: {
				files: [
					{expand:true, cwd:'<%= LIB_PATH %>underscore', src:'underscore-min.js', dest:'<%= JS_PATH %>'},
					{expand:true, cwd:'<%= LIB_PATH %>jquery', src:'jquery.min.js', dest:'<%= JS_PATH %>'},
					{expand:true, cwd:'<%= LIB_PATH %>bootstrap/dist', src:'**', dest:'<%= BASE_PATH %>'},
					{expand:true, cwd:'./src', src:'*.js', dest:'<%= JS_PATH %>src/'}
				]
			}
		},
		watch: {
			scripts: {
				files: ['./src/*.js'],
				tasks: ['product'],
				options: {
					spawn: true,
				}
			}
		}
	});

	//grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	//grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('lib', ['copy']);

	//grunt.registerTask('product', ['concat:dist','uglify','clean']);
	grunt.registerTask('product', ['lib','uglify']);
	grunt.registerTask('development', ['concat:dev']);

	grunt.registerTask('default', ['lib','product']);
};