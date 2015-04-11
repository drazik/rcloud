var gulp = require('gulp');
var plumber = require('gulp-plumber');
var imagemin = require('gulp-imagemin');

gulp.task('images', function() {
    gulp.src('assets/img/**/*')
        .pipe(plumber())
        .pipe(imagemin())
        .pipe(gulp.dest('web/assets/img'));
});