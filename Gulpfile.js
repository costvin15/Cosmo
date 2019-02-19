var gulp = require("gulp");
var gulp_sass = require("gulp-sass");
var gulp_clean_css = require("gulp-clean-css");
var gulp_rename = require("gulp-rename");
var gulp_concat = require("gulp-concat");
var gulp_uglify = require("gulp-uglify");

var sass_locations = [
    "./src/scss/**/*.scss"
];

var js_locations = [
    "./node_modules/popper.js/dist/umd/popper.js",
    "./node_modules/bootstrap/dist/js/bootstrap.js"
]

gulp.task("sass", gulp.series(() => {
    return gulp.src(sass_locations)
        .pipe(gulp_sass())
        .pipe(gulp.dest("./public/dist/css"))
        .pipe(gulp_clean_css({compatibility: "ie8", debug: true}, (details) => {
            console.log(details.name + ": Tamanho original = " + details.stats.originalSize + ", Novo tamanho: " + details.stats.minifiedSize);
        }))
        .pipe(gulp_rename({
            suffix: ".min"
        }))
        .pipe(gulp.dest("./public/dist/css"));
}));

gulp.task("js", gulp.series(() => {
    return gulp.src(js_locations)
        .pipe(gulp_concat("script.js"))
        .pipe(gulp.dest("./public/dist/js"))
        .pipe(gulp_rename({
            suffix: ".min"
        }))
        .pipe(gulp_uglify())
        .pipe(gulp.dest("./public/dist/js"));
}));

gulp.task("watch", gulp.series(() => {
    gulp.watch(sass_locations, gulp.parallel(["sass", "js"]));
}));

gulp.task("default", gulp.series(["watch"]));