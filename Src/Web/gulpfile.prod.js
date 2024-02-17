import gulp from "gulp";
import esbuild from "esbuild";
import autoprefixer from "gulp-autoprefixer";
import cleanCSS from "gulp-clean-css";
import rename from "gulp-rename";
import dartSass from "sass";
import gulpSass from "gulp-sass";
const sass = gulpSass(dartSass);

gulp.task("build-js", function (done) {
    esbuild.build({
        entryPoints: ["./App/src/wwwroot/js/pages/**/*.js"],
        bundle: true,
        minify: true,
        sourcemap: false,
        outdir: "./App/public/js",
    }).then(() => done()).catch(() => done("Build failed"));
});

gulp.task("build-js-components", function (done) {
    esbuild.build({
        entryPoints: ["./App/src/wwwroot/js/components/main.js"],
        bundle: true,
        minify: true,
        sourcemap: false,
        outfile: "./App/public/js/components.js",
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

gulp.task("default", gulp.parallel("scss", "build-js", "build-js-components", "build-css"));
