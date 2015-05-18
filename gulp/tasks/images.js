var gulp = require('gulp');

gulp.task('images', function() {
    gulp.src('assets/img/**/*')
        .pipe(gulp.dest('web/assets/img'));
});