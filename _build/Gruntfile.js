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
				 '*/',
				 ''
		],

		concat: {
			dist: {
				src: ['./src/main.js','./src/quizPool.js'],
				dest: './tmp/main.js'
			},
			dev: {
				src: ['./src/main.js','./src/quizPool.js'],
				dest: '<%= JS_PATH %>'+'main.min.js'
			}
		},
		clean: {
			dist: ['./tmp']
		},
		uglify: {
			options: {
				banner: '<%= banner.join("\\n") %>'
			},
			dist: {
				src: ['./tmp/main.js'],
				dest: '<%= JS_PATH %>'+'main.min.js'
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

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-clean');

	grunt.registerTask('lib', ['copy']);

	grunt.registerTask('product', ['concat:dist','uglify','clean']);
	grunt.registerTask('development', ['concat:dev']);

	grunt.registerTask('default', ['lib','product']);
};