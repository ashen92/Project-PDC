const gulp = require("gulp");
const esbuild = require("esbuild");
const sass = require("gulp-sass")(require("sass"));
const autoprefixer = require("gulp-autoprefixer");
const cleanCSS = require("gulp-clean-css");
const rename = require("gulp-rename");

gulp.task("build-js", function (done) {
    esbuild.build({
        entryPoints: ["./App/src/wwwroot/js/*.js"],
        bundle: true,
        minify: true,
        sourcemap: false,
        outdir: "./App/public/js",
    }).then(() => done()).catch(() => done("Build failed"));
});

gulp.task("scss", function () {
    return gulp.src("./App/src/wwwroot/scss/main.scss")
        .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
        .pipe(autoprefixer({
            overrideBrowserslist: [
                ">0.15%",
                "defaults",
                "not dead"
            ],
            cascade: false
        }))
        .pipe(gulp.dest("./App/public/css"));
});

gulp.task("build-css", () => {
    return gulp.src("./App/src/wwwroot/css/*.css")
        .pipe(cleanCSS({ compatibility: "ie8" }))
        .pipe(rename({
            suffix: ".min"
        }))
        .pipe(gulp.dest("./App/public/css"));
});

gulp.task("default", gulp.parallel("scss", "build-js"));
