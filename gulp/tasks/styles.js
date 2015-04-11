var gulp = require('gulp');
var sass = require('gulp-sass');
var plumber = require('gulp-plumber');

gulp.task('styles', function() {
    gulp.src('assets/scss/main.scss')
        .pipe(plumber())
        .pipe(sass())
        .pipe(gulp.dest('web/assets/css'));
});