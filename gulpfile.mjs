// Importar módulos necesarios

import * as dartSass from "sass";

import avif from "imagemin-avif";
import gulp from "gulp";
import gulpBabel from "gulp-babel";
import gulpPlumber from "gulp-plumber";
import gulpSass from "gulp-sass";
import imagemin from "gulp-imagemin";
import terser from "gulp-terser";
import webp from "gulp-webp";

// Desestructurar métodos de gulp
const { dest, parallel, series, src, watch } = gulp;

// Configurar gulp-sass para usar dart-sass
const sass = gulpSass(dartSass);

// Rutas de los archivos fuente
const paths = {
	scss: "src/scss/**/*.scss",
	js: "src/js/**/*.js",
	img: "src/img/**/*",
};

// Opciones para la optimización de imágenes
const opcionesImagen = { quality: 50 };

// Tarea para compilar archivos SCSS a CSS
export function css(done) {
	src(paths.scss, { sourcemaps: true })
		.pipe(gulpPlumber()) // Prevenir que errores detengan la ejecución de Gulp
		.pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError)) // Compilar SCSS a CSS y comprimir
		.pipe(dest("./public/dist/css", { sourcemaps: "." })); // Guardar el CSS compilado con sourcemaps
	done();
}

// Tarea para compilar y minificar archivos JavaScript
export function js(done) {
	src(paths.js, { sourcemaps: true })
		.pipe(gulpPlumber()) // Prevenir que errores detengan la ejecución de Gulp
		.pipe(gulpBabel({ presets: ["@babel/preset-env"] })) // Transpilar JS con Babel
		.pipe(terser()) // Minificar el JS
		.pipe(dest("./public/dist/js")); // Guardar el JS minificado
	done();
}

// Tarea para optimizar imágenes
export function img(done) {
	src(paths.img)
		.pipe(imagemin()) // Optimizar imágenes
		.pipe(dest("./public/dist/img")); // Guardar las imágenes optimizadas
	done();
}

// Tarea para convertir imágenes a formato WebP
export function versionWebp(done) {
	src("src/img/*.{png,jpg}")
		.pipe(webp(opcionesImagen)) // Convertir imágenes a WebP
		.pipe(dest("./public/dist/img")); // Guardar las imágenes en formato WebP
	done();
}

// Tarea para convertir imágenes a formato AVIF
export function versionAvif(done) {
	src("src/img/*.{png,jpg}")
		.pipe(imagemin([avif(opcionesImagen)])) // Convertir imágenes a AVIF
		.pipe(dest("./public/dist/img")); // Guardar las imágenes en formato AVIF
	done();
}

// Tarea para observar cambios en los archivos y ejecutar las tareas correspondientes
export function dev() {
	watch(paths.scss, css); // Observar cambios en archivos SCSS y ejecutar la tarea css
	watch(paths.js, js); // Observar cambios en archivos JS y ejecutar la tarea js
	watch(paths.img, series(img, versionWebp, versionAvif)); // Observar cambios en imágenes y ejecutar las tareas img, versionWebp y versionAvif
}

// Tarea por defecto que se ejecuta al correr `gulp`
export default parallel(css, img, versionWebp, versionAvif, js, dev);
