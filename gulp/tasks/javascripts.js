'use strict';

var gulp = require('gulp');
var browserify = require('browserify');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');

gulp.task('javascripts', function() {
	var b = browserify({
		entries: './assets/js/editorapp.js',
		debug: true
	});

	return b.bundle()
		.pipe(source('editorapp.js'))
		.pipe(buffer())
		.pipe(gulp.dest('./web/assets/js'));
});