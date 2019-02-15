var gulp = require("gulp");
var gulp_sass = require("gulp-sass");
var gulp_cssmin = require("gulp-cssmin");
var gulp_rename = require("gulp-rename");

var sass_locations = [
    "./src/scss/**/*.scss"
];

gulp.task("sass", gulp.series(() => {
    return gulp.src(sass_locations)
        .pipe(gulp_sass())
        .pipe(gulp.dest("./public/dist/css"))
        .pipe(gulp_cssmin())
        .pipe(gulp_rename({
            suffix: ".min"
        }))
        .pipe(gulp.dest("./public/dist/css"));
}));

gulp.task("watch", gulp.series(() => {
    gulp.watch(sass_locations, gulp.parallel(["sass"]));
}));

gulp.task("default", gulp.series(["watch"]));