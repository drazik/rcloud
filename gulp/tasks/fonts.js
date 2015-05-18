var gulp = require('gulp');

gulp.task('fonts', function() {
    gulp.src('assets/fonts/**/*')
        .pipe(gulp.dest('web/assets/fonts'));
});