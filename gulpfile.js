const gulp = require('gulp')
const sass = require('gulp-sass')
const uglify = require('gulp-uglify')
const rename = require('gulp-rename')
const cssmin = require('gulp-clean-css')
const browserify = require('browserify')
const sourcemaps = require('gulp-sourcemaps')
const source = require('vinyl-source-stream')

gulp.task('css', function () {
  return gulp.src('./assets/sass/[^_]*.scss')
    .pipe(sass())
    .pipe(rename({ extname: '.css' }))
    .pipe(gulp.dest('./assets/css'))
})

gulp.task('minify-css', gulp.series('css', function () {
  return gulp.src(['./assets/css/*.css', '!./assets/css/*.min.css'])
    .pipe(sourcemaps.init({ loadMaps: true }))
    .pipe(cssmin())
    .pipe(rename({ extname: '.min.css' }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('./assets/css'))
}))

gulp.task('js', gulp.parallel(['public.js', 'gutenberg-block.js', 'admin/admin.js'].map(f => () => {
  return browserify({ entries: 'assets/browserify/' + f })
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
    .pipe(gulp.dest('./assets/js'))
})))

gulp.task('minify-js', gulp.series('js', function () {
  return gulp.src(['./assets/js/**/*.js', '!./assets/js/**/*.min.js'])
    .pipe(sourcemaps.init({ loadMaps: true }))
    .pipe(uglify())
    .pipe(rename({ extname: '.min.js' }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('./assets/js'))
}))

gulp.task('default', gulp.parallel('minify-css', 'minify-js'))

gulp.task('watch', function () {
  gulp.watch('./assets/sass/**/*.scss', gulp.series('css'))
  gulp.watch('./assets/browserify/**/*.js', gulp.series('js'))
})
