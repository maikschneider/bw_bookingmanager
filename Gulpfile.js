var gulp = require('gulp');
var $ = require('gulp-load-plugins')();

function sass() {
  return gulp.src('Resources/Private/Scss/administration-*.scss')
    .pipe($.sass({
      outputStyle: 'compressed' // if css compressed **file size**
    })
      .on('error', $.sass.logError))
    .pipe(gulp.dest('Resources/Public/Css'));
};

function watch() {
  gulp.watch("Resources/Private/Scss/**/*.scss", sass);
}

gulp.task('sass', sass);
gulp.task('default', gulp.series('sass', watch));
