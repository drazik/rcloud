'use strict';

var gulp = require('gulp');
var browserify = require('browserify');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var reactify = require('reactify');

gulp.task('javascripts', function() {
	var b = browserify({
		entries: './assets/js/editorapp.jsx',
		debug: true,
		transform: [reactify]
	});

	return b.bundle()
		.pipe(source('editorapp.js'))
		.pipe(buffer())
		.pipe(gulp.dest('./web/assets/js'));
});