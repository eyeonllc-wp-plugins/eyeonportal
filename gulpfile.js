const gulp = require('gulp');
const sass = require('gulp-sass');

gulp.task('main-styles', function () {
  return gulp.src('./assets/scss/**/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./assets/css/'));
});

gulp.task('elementor-styles', function () {
  return gulp.src('./elementor/scss/**/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./elementor/css'));
});