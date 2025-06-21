-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-06-2025 a las 18:15:45
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `biblioteca_fd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `genre` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `pdf_path` varchar(255) NOT NULL,
  `url_resumen` varchar(255) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `publicado` varchar(2) DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `year`, `genre`, `image_path`, `description`, `pdf_path`, `url_resumen`, `uploaded_by`, `publicado`) VALUES
(7, 'La enseñanza de la ciencia naturales basadas en proyectos', 'Autor por definir', 2025, 'Educación, Ciencias Naturales', 'assets/temp_uploads/cover_685419cc3d908.png', 'Metodología para el trabajo por proyectos en Ciencias Naturales, enfocado en el aprendizaje activo y la investigación estudiantil.', 'assets/books/pdf_685419cc3d683.pdf', 'https://ensenanza-ciencias-proye-tgtg85h.gamma.site/', NULL, 'si'),
(8, 'El juego de la formación docente', 'Autor por definir', 2024, 'Educación, Metodología', 'assets/temp_uploads/cover_685422da82b4c.png', 'Enfoques metodológicos modernos para la educación contemporánea.', 'assets/temp_uploads/pdf_685422da82738.pdf', NULL, NULL, 'si'),
(9, 'Antología de Cuentos', 'Varios Autores', 2025, 'Literatura, Educación', 'assets/temp_uploads/cover_6854240140fbf.png', 'Selección de cuentos para el desarrollo de la comprensión lectora y el análisis literario.', 'assets/temp_uploads/pdf_6854240140cdf.pdf', 'https://antologia-literaria-y2urvlc.gamma.site/', NULL, 'si'),
(10, 'Aprender a Enseñar con TIC en Educación Superior', 'Díaz, Autor', 2024, 'Educación, Tecnología', 'assets/temp_uploads/cover_68543fc505792.png', 'Guía para la integración de las Tecnologías de la Información y Comunicación en la educación superior.', 'assets/temp_uploads/pdf_68543fc505536.pdf', 'https://tic-ensenanza-7hkzmwz.gamma.site/', NULL, 'si'),
(12, 'Cómo Planificar y Evaluar en el Aula', 'Brío, C.', 2024, 'Educación, Planificación', 'assets/temp_uploads/cover_685441767c8b3.png', 'Estrategias prácticas para la planificación y evaluación efectiva en el ambiente escolar.', 'assets/temp_uploads/pdf_685441767c5d5.pdf', 'https://guia-para-docentes-67x398e.gamma.site/', NULL, 'si'),
(13, 'Catálogo de Herramientas Digitales', 'Autor por definir', 2024, 'Educación, Tecnología', 'assets/temp_uploads/cover_685442242e4ce.png', 'Compendio de herramientas digitales útiles para la educación moderna y su implementación práctica.', 'assets/temp_uploads/pdf_685442242e18d.pdf', 'https://catalogo-herramientas-di-th9arw0.gamma.site/', NULL, 'si'),
(14, 'Waldorf - Un Espacio para Aprender a Decir y Escuchar', 'COM - Waldorf', 2024, 'Educación, Pedagogía Waldorf', 'assets/temp_uploads/cover_685443754fead.png', 'Metodología Waldorf centrada en el desarrollo de la comunicación y la escucha activa en el proceso educativo.', 'assets/temp_uploads/pdf_685443754fc39.pdf', 'https://ensenanza-lengua-oral-b05pp6c.gamma.site/', NULL, 'si'),
(15, 'Cómo Abordar la Educación del Futuro', 'Gisbert', 2024, 'Educación, Futuro', 'assets/temp_uploads/cover_6854444f44b0f.jpg', 'Perspectivas y estrategias para enfrentar los desafíos educativos del siglo XXI.', 'assets/temp_uploads/pdf_6854444f44892.pdf', 'https://transformacion-digital-e-1jesp1f.gamma.site/', NULL, 'si'),
(17, 'Cómo Dar Clases a los que No Quieren', 'Vaello', 2024, 'Educación, Motivación', 'assets/temp_uploads/cover_68544636f0ce4.jpg', 'Estrategias y técnicas para motivar y trabajar con estudiantes desmotivados o reticentes al aprendizaje.', 'assets/temp_uploads/pdf_68544636f0a6a.pdf', 'https://ensenar-a-querer-aprende-2lcddfa.gamma.site/', NULL, 'si'),
(18, 'Secuencias Didácticas. Aprendizaje y Evaluación de Competencias', 'Sergio Tobón Tobón, Julio Herminio Pimienta Rico y Juan Antonio García Fraile', 2025, 'Educación, Didáctica', 'assets/temp_uploads/cover_68544921f3e70.png', 'Guía práctica sobre la elaboración de secuencias didácticas enfocadas en el aprendizaje y evaluación de competencias en el aula.', 'assets/temp_uploads/pdf_68544921f3afa.pdf', 'https://secuencias-didacticas-gjs0gma.gamma.site/', NULL, 'si'),
(19, 'El Acompañamiento Pedagógico', 'Taveras', 2024, 'Educación, Acompañamiento', 'assets/temp_uploads/cover_685449ab39fa1.png', 'Estrategias y metodologías para el acompañamiento pedagógico efectivo, centrado en el desarrollo profesional docente y la mejora continua.', 'assets/temp_uploads/pdf_685449ab39ceb.pdf', 'https://acompanamiento-pedagogic-nqfptpi.gamma.site/', NULL, 'si'),
(20, 'Modelos pedagógicos y teorías del aprendizaje', 'Alexander Ortiz Ocaña', 2024, 'Educación, Pedagogía', 'assets/temp_uploads/cover_68544b5e387bb.png', 'Análisis de los modelos pedagógicos y teorías del aprendizaje más relevantes en la educación contemporánea, con aplicaciones prácticas.', 'assets/temp_uploads/pdf_68544b5e38504.pdf', 'https://modelos-pedagogicos-pfxoz2p.gamma.site/', NULL, 'si'),
(21, 'Pedagogía de la Tolerancia', 'Paulo Freire', 2024, 'Educación, Filosofía', 'assets/temp_uploads/cover_68544b71c87b7.png', 'Reflexiones de Paulo Freire sobre la importancia de la tolerancia en la educación y su impacto en la formación integral del individuo.', 'assets/temp_uploads/pdf_68544b71c8504.pdf', 'https://pedagogia-tolerancia-fre-iyy28ld.gamma.site/', NULL, 'si'),
(22, 'Metodología de la Gestión Curricular', 'Tobón', 2024, 'Educación, Currículo', 'assets/temp_uploads/cover_68544b9046080.png', 'Enfoques y estrategias para la gestión curricular efectiva, promoviendo una educación de calidad y pertinente.', 'assets/temp_uploads/pdf_68544b9045cf5.pdf', 'https://metodologia-gestion-curr-085hhwy.gamma.site/', NULL, 'si'),
(23, 'Didáctica del Lenguaje y la Literatura', 'Autor por definir', 2024, 'Educación, Literatura', 'assets/temp_uploads/cover_68544bce49aa9.jpg', 'Metodologías especializadas para la enseñanza del lenguaje y la literatura en diferentes niveles educativos.', 'assets/temp_uploads/pdf_68544bce4980e.pdf', 'https://didactica-lenguaje-liter-dd4vzc0.gamma.site/', NULL, 'si'),
(25, 'Mini manual de Herramientas Google para Educación', 'Autor por definir', 2024, 'Educación, Tecnología', 'assets/temp_uploads/cover_68544cec91edb.png', 'Guía práctica sobre el uso de herramientas Google en el ámbito educativo, facilitando la integración tecnológica en el aula.', 'assets/temp_uploads/pdf_68544cec91c25.pdf', 'https://herramientas-google-educ-oh4fwu9.gamma.site/', NULL, 'si'),
(26, 'El Futuro de las Escuelas y la Formación de Maestros', 'Eduardo Andere', 2024, 'Educación, Formación Docente', 'assets/temp_uploads/cover_68544d750945c.jpg', 'Análisis prospectivo sobre cómo evolucionarán las escuelas y la formación de docentes en el futuro cercano.', 'assets/temp_uploads/pdf_68544d75091ab.pdf', 'https://el-futuro-de-las-escuela-8w0o4dx.gamma.site/', NULL, 'si'),
(27, 'Enfoques pedagógicos contemporáneos y posmodernos', 'Sara Farfán Cruz', 2024, 'Educación, Pedagogía', 'assets/temp_uploads/cover_68544d9b0a23b.png', 'Exploración de los enfoques pedagógicos contemporáneos y posmodernos, con énfasis en su aplicación práctica.', 'assets/temp_uploads/pdf_68544d9b09f83.pdf', 'https://enfoques-pedagogicos-p9v979f.gamma.site/', NULL, 'si'),
(28, 'Transformando las prácticas de evaluación a través del trabajo colaborativa', 'Raúl Barrantes Clavijo', 2024, 'Educación, Práctica Pedagógica', 'assets/temp_uploads/cover_68544df37c95e.jpg', 'Análisis comprensivo de la práctica pedagógica y su impacto en la formación integral de maestros.', 'assets/temp_uploads/pdf_68544df37c6b2.pdf', 'https://transformando-evaluacion-w2vxt91.gamma.site/', NULL, 'si'),
(29, 'Procesos Didácticos', 'Raúl Barrantes Clavijo', 2024, 'Educación, Práctica Pedagógica', 'assets/temp_uploads/cover_68544e5260761.jpg', 'Análisis comprensivo de la práctica pedagógica y su impacto en la formación integral de maestros.', 'assets/temp_uploads/pdf_68544e52604aa.pdf', 'https://dominando-procesos-didac-dz5s3y5.gamma.site/', NULL, 'si'),
(30, 'Pensar la Formación de Maestros Hoy', 'María Cristina Martínez Pineda et al.', 2024, 'Educación, Formación Docente', 'assets/temp_uploads/cover_68544ec6b3a86.png', 'Propuesta pedagógica desde la experiencia para repensar la formación de maestros en la actualidad.', 'assets/temp_uploads/pdf_68544ec6b37b3.pdf', 'https://repensar-formacion-docen-q3dd39y.gamma.site/', NULL, 'si'),
(32, 'Pedagogía del Juego - Traducción al Español', 'Autor por definir', 2024, 'Educación, Pedagogía Lúdica', 'assets/temp_uploads/cover_68544fe307143.png', 'Enfoques pedagógicos basados en el juego como herramienta fundamental para el aprendizaje.', 'assets/temp_uploads/pdf_68544fe306e80.pdf', 'https://pedagogia-del-juego-d2tc0xk.gamma.site/', NULL, 'si'),
(33, 'Innovar en Educación, Sí Pero Cómo', 'Tricot', 2024, 'Educación, Innovación', 'assets/temp_uploads/cover_68544ff06bc31.png', 'Guía práctica para implementar innovaciones efectivas en el ámbito educativo.', 'assets/temp_uploads/pdf_68544ff06b961.pdf', 'https://innovar-en-educacion-xmanh7a.gamma.site/', NULL, 'si'),
(34, 'Audífon de Haro - Ideas de Literatura y Teoría de Géneros Literarios', 'Audífon de Haro', 2016, 'Literatura, Teoría Literaria', 'assets/temp_uploads/cover_685457e80697e.png', 'Análisis profundo de la teoría de los géneros literarios y su aplicación en la enseñanza de la literatura.', 'assets/temp_uploads/pdf_685457e8066c9.pdf', 'https://teoria-generos-literario-9j85q07.gamma.site/', NULL, 'si'),
(35, 'Enseñar Hoy', 'Carrasco', 2024, 'Educación, Metodología', 'assets/temp_uploads/cover_685458e065fdc.jpg', 'Reflexiones y propuestas para la enseñanza en el contexto educativo actual.', 'assets/temp_uploads/pdf_685458e065cec.pdf', 'https://ensenar-hoy-k3skhr4.gamma.site/', NULL, 'si'),
(36, 'Libro de Prácticas y Diagnósticos - Ciencias Naturales', 'Autor por definir', 2024, 'Educación, Ciencias Naturales', 'assets/temp_uploads/cover_68545a63a53e1.jpg', 'Material práctico para el diagnóstico y evaluación en ciencias naturales, con actividades experimentales.', 'assets/temp_uploads/pdf_68545a63a5142.pdf', 'https://practica-pedagogica-3t73u77.gamma.site/', NULL, 'si');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_admin`) VALUES
(1, 'Lucas Britez', 'lucasandresbritezalvarez@gmail.com', '$2y$10$Z4jIvI6OF6K1qMEo7Bqc.ehRgedfj/SNFWFrm415nTmYQde.uhage', 1),
(2, 'Lua', 'luanarolon111007@gmail.com', '$2y$10$CMp1G27nONM5QMBmEQM2W.QpNbNjoEQrsCKXimVsHMeLrbxoyfQvi', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
