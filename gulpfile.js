const gulp = require('gulp')
const uglify = require('gulp-uglify')
const rename = require('gulp-rename')
const cssmin = require('gulp-clean-css')
const browserify = require('browserify')
const source = require('vinyl-source-stream')
const streamify = require('gulp-streamify')
const noop = require('through2').obj
const minify = process.env.NODE_ENV !== 'development'

gulp.task('css', function () {
  return gulp.src(['./assets/src/css/*.css'])
    .pipe(minify ? cssmin() : noop())
    .pipe(rename({ extname: '.css' }))
    .pipe(gulp.dest('./assets/css'))
})

gulp.task('js', gulp.parallel(['public.js', 'gutenberg-block.js', 'admin/admin.js'].map(f => () => {
  return browserify({ entries: 'assets/src/js/' + f })
    .transform('babelify', {
      presets: [
        ['@babel/preset-env', { targets: '> 0.25%, not dead', forceAllTransforms: true }]
      ],
      plugins: [
        ['@babel/plugin-proposal-decorators', { legacy: true }],
        ['@babel/plugin-transform-react-jsx', { pragma: f === 'gutenberg-block.js' ? 'React.createElement' : 'h' }]
      ]
    })
    .bundle()
    .pipe(source(f.split('/').pop()))
    .pipe(minify ? streamify(uglify()) : noop())
    .pipe(gulp.dest('./assets/js'))
})))

gulp.task('img', () => {
  return gulp.src('assets/src/img/*')
    .pipe(gulp.dest('assets/img'))
})

gulp.task('default', gulp.parallel('css', 'js', 'img'))

gulp.task('watch', function () {
  gulp.watch('./assets/src/css/*.css', gulp.series('css'))
  gulp.watch('./assets/src/js/**/*.js', gulp.series('js'))
})
