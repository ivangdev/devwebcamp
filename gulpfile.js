// Importar módulos necesarios
const dartSass = require("sass");
const gulp = require("gulp");
const gulpBabel = require("gulp-babel");
const gulpPlumber = require("gulp-plumber");
const gulpSass = require("gulp-sass");
const imagemin = require("gulp-imagemin");
const sharpResponsive = require("gulp-sharp-responsive"); // New import
const terser = require("gulp-terser");
const webp = require("gulp-webp");
const autoprefixer = require("autoprefixer");
const postcss = require("gulp-postcss");

// Configurar gulp-sass para usar dart-sass como compilador
const sass = gulpSass(dartSass);

/**
 * @file Configuración de Gulp para automatización de tareas
 * @description Sistema de compilación y optimización de assets (CSS, JS, imágenes)
 */

// Desestructurar métodos de gulp para mejor legibilidad
/**
 * Desestructuración de métodos principales de Gulp
 * @type {Object}
 * @property {Function} dest - Función para especificar destino de archivos
 * @property {Function} parallel - Ejecuta tareas en paralelo
 * @property {Function} series - Ejecuta tareas en serie
 * @property {Function} src - Función para especificar origen de archivos
 * @property {Function} watch - Observa cambios en archivos
 */
const { dest, parallel, series, src, watch } = gulp;

/**
 * Configuración del manejador de errores global
 * Evita que los errores detengan el proceso de Gulp
 * @type {Object}
 */
const errorHandler = {
	errorHandler: function (err) {
		console.log(`Error: ${err.message}`);
		this.emit("end");
	},
};

/**
 * Configuración de rutas del proyecto
 * @type {Object}
 * @property {string} scss - Ruta de archivos SCSS
 * @property {string} js - Ruta de archivos JavaScript
 * @property {Object} img - Rutas de imágenes
 * @property {Object} dist - Rutas de destino para archivos compilados
 */
const paths = {
	scss: "src/scss/**/*.scss",
	js: "src/js/**/*.js",
	img: {
		all: "src/img/**/*",
		raster: "src/img/**/*.{png,jpg,jpeg}",
	},
	dist: {
		css: "./public/dist/css",
		js: "./public/dist/js",
		img: "./public/dist/img",
	},
};

/**
 * Opciones de compresión para imágenes
 * @type {Object}
 */
const opcionesImagen = { quality: 50 };

/**
 * Compila archivos SCSS a CSS
 * - Genera sourcemaps
 * - Maneja errores sin detener el proceso
 * - Comprime el CSS resultante
 * @param {Function} done - Callback de finalización
 */
function css(done) {
	src(paths.scss, { sourcemaps: true })
		.pipe(gulpPlumber(errorHandler))
		.pipe(sass({ outputStyle: "compressed" }))
		.pipe(postcss([autoprefixer()]))
		.pipe(dest(paths.dist.css, { sourcemaps: "." }))
		.on("end", done);
}

/**
 * Procesa archivos JavaScript
 * - Transpila código moderno con Babel
 * - Minifica el código
 * - Genera sourcemaps
 * @param {Function} done - Callback de finalización
 */
function js(done) {
	src(paths.js, { sourcemaps: true })
		.pipe(gulpPlumber(errorHandler))
		.pipe(gulpBabel({ presets: ["@babel/preset-env"] }))
		.pipe(terser())
		.pipe(dest(paths.dist.js))
		.on("end", done);
}

/**
 * Optimiza imágenes
 * - Comprime JPG y PNG
 * - Mantiene la mejor calidad posible
 * @param {Function} done - Callback de finalización
 */
function img(done) {
	src(paths.img.all)
		.pipe(gulpPlumber(errorHandler))
		.pipe(
			imagemin([
				imagemin.gifsicle({ interlaced: true }),
				imagemin.mozjpeg({ quality: 75, progressive: true }),
				imagemin.optipng({ optimizationLevel: 5 }),
			]),
		)
		.pipe(dest(paths.dist.img))
		.on("end", done);
}

/**
 * Convierte imágenes a formato WebP
 * - Aplica compresión según opcionesImagen
 * @param {Function} done - Callback de finalización
 */
function versionWebp(done) {
	src(paths.img.raster)
		.pipe(gulpPlumber(errorHandler))
		.pipe(webp({ quality: 80 }))
		.pipe(dest(paths.dist.img))
		.on("end", done);
}

/**
 * Convierte imágenes a formato AVIF
 * - Mantiene dimensiones originales
 * - Aplica compresión de calidad 50
 * @param {Function} done - Callback de finalización
 */
function versionAvif(done) {
	src(paths.img.raster)
		.pipe(gulpPlumber(errorHandler))
		.pipe(
			sharpResponsive({
				formats: [
					{
						format: "avif",
						quality: 80,
					},
				],
			}),
		)
		.pipe(dest(paths.dist.img))
		.on("end", done);
}

/**
 * Agrupa todas las tareas de procesamiento de imágenes
 * Ejecuta en paralelo: optimización, WebP y AVIF
 * @type {Function}
 */
const imageTasks = parallel(img, versionWebp, versionAvif);

/**
 * Agrupa tareas de procesamiento de assets
 * Ejecuta en paralelo: CSS y JavaScript
 * @type {Function}
 */
const assetTasks = parallel(css, js);

/**
 * Modo desarrollo
 * Observa cambios en archivos y ejecuta tareas correspondientes
 * - SCSS: recompila cada 500ms
 * - JS: recompila cada 500ms
 * - Imágenes: procesa cada 1000ms
 */
function dev() {
	watch(paths.scss, { delay: 500 }, css);
	watch(paths.js, { delay: 500 }, js);
	watch(paths.img.all, { delay: 1000 }, imageTasks);
}

/**
 * Build de producción
 * Ejecuta procesamiento de assets seguido de imágenes
 * @type {Function}
 */
const build = series(assetTasks, series(img, versionWebp, versionAvif));

// Exportar las tareas
exports.css = css;
exports.js = js;
exports.img = img;
exports.versionWebp = versionWebp;
exports.versionAvif = versionAvif;
exports.dev = dev;
exports.default = series(build, dev);
