<?php
/* EduFolio - Landing page publica. */
require_once __DIR__ . '/../app/auth.php';

if (esta_autenticado()) {
    redirigir('dashboard.php');
}

$titulo      = 'Inicio';
$vista_app   = false;
$ocultar_pie = true;
require __DIR__ . '/../app/layout/header.php';
?>

<!-- ===================== NAVBAR ===================== -->
<nav class="nav" id="nav">
    <div class="contenedor nav-inner">
        <a class="marca" href="index.php"><img class="marca-img" src="img/logo.png" alt="<?= APP_NAME ?>"></a>
        <ul class="nav-links" id="navLinks">
            <li><a href="#inicio">Inicio</a></li>
            <li><a href="#caracteristicas">Caracteristicas</a></li>
            <li><a href="#secciones">Secciones</a></li>
            <li><a href="#como">Como funciona</a></li>
            <li><a href="#testimonios">Testimonios</a></li>
        </ul>
        <div class="nav-acciones">
            <a class="btn btn-outline btn-sm" href="login.php">Iniciar sesion</a>
            <a class="btn btn-primario btn-sm" href="registro.php">Crear cuenta <i class="bi bi-arrow-right"></i></a>
        </div>
        <button class="nav-burger" id="navBurger" aria-label="Menu"><i class="bi bi-list"></i></button>
    </div>
</nav>

<!-- ===================== HERO ===================== -->
<header class="hero" id="inicio">
    <div class="hero-bg"><span class="blob b1"></span><span class="blob b2"></span><span class="blob b3"></span></div>
    <div class="contenedor hero-grid">
        <div class="hero-texto reveal">
            <h1>Tu trabajo docente, <span class="texto-grad">organizado y a un clic</span>.</h1>
            <p class="lede">EduFolio reune tus documentos, notas, material didactico y tareas
               en un solo espacio seguro y accesible desde cualquier dispositivo. Deja atras
               los archivos dispersos.</p>
            <div class="hero-cta">
                <a class="btn btn-primario btn-lg" href="registro.php">Comenzar gratis <i class="bi bi-arrow-right"></i></a>
                <a class="btn btn-outline btn-lg" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Iniciar sesion</a>
            </div>
            <div class="hero-mini">
                <span><i class="bi bi-check-circle-fill"></i> Sin costo</span>
                <span><i class="bi bi-check-circle-fill"></i> Datos cifrados</span>
                <span><i class="bi bi-check-circle-fill"></i> Acceso 24/7</span>
            </div>
        </div>
        <div class="hero-visual reveal d2">
            <div class="mock">
                <div class="mock-bar"><span></span><span></span><span></span></div>
                <div class="mock-body">
                    <div class="mock-tile t1"><i class="bi bi-folder-fill"></i><b>Documentos</b></div>
                    <div class="mock-tile t2"><i class="bi bi-journal-text"></i><b>Notas</b></div>
                    <div class="mock-tile t3"><i class="bi bi-easel2-fill"></i><b>Material</b></div>
                    <div class="mock-tile t4"><i class="bi bi-check2-square"></i><b>Tareas</b></div>
                </div>
            </div>
            <div class="chip-flotante chip-1"><i class="bi bi-shield-lock-fill"></i> Seguro</div>
            <div class="chip-flotante chip-2"><i class="bi bi-cloud-check-fill"></i> Siempre disponible</div>
        </div>
    </div>
</header>

<!-- ===================== STATS ===================== -->
<section class="seccion stats">
    <div class="contenedor stats-grid">
        <div class="stat reveal"><div class="num" data-count="4">0</div><div class="lbl">Secciones integradas</div></div>
        <div class="stat reveal d1"><div class="num" data-count="100" data-suffix="%">0</div><div class="lbl">Gratuito, sin anuncios</div></div>
        <div class="stat reveal d2"><div class="num" data-count="24" data-suffix="/7">0</div><div class="lbl">Acceso disponible</div></div>
        <div class="stat reveal d3"><div class="num">&infin;</div><div class="lbl">Sin limite de archivos</div></div>
    </div>
</section>

<!-- ===================== CARACTERISTICAS / ABOUT ===================== -->
<section class="seccion" id="caracteristicas">
    <div class="contenedor features-grid">
        <div class="reveal">
            <span class="etiqueta"><i class="bi bi-patch-check-fill"></i> Por que EduFolio</span>
            <h2 class="sec-titulo">Todo tu trabajo docente, en un mismo lugar</h2>
            <p class="sec-sub">Pensado para profesores que necesitan orden y respaldo, sin la
               complejidad de una plataforma de cursos completa.</p>
            <ul class="feature-lista">
                <li><span class="ic bg-indigo"><i class="bi bi-collection-fill"></i></span>
                    <div><strong>Centralizado</strong><p>Tus recursos reunidos y clasificados por tipo.</p></div></li>
                <li><span class="ic bg-naranja"><i class="bi bi-shield-lock-fill"></i></span>
                    <div><strong>Seguro</strong><p>Contrasenas cifradas y proteccion contra ataques comunes.</p></div></li>
                <li><span class="ic bg-cian"><i class="bi bi-phone-fill"></i></span>
                    <div><strong>Accesible</strong><p>Funciona en computadora, tablet y telefono.</p></div></li>
                <li><span class="ic bg-morado"><i class="bi bi-lightning-charge-fill"></i></span>
                    <div><strong>Sencillo</strong><p>Interfaz clara, sin curva de aprendizaje.</p></div></li>
            </ul>
        </div>
        <div class="collage reveal d2">
            <div class="pieza"><i class="bi bi-folder-fill"></i><b>Resguarda tus documentos</b></div>
            <div class="pieza"><i class="bi bi-journal-text"></i><b>Apuntes rapidos</b></div>
            <div class="pieza"><i class="bi bi-easel2-fill"></i><b>Material por materia</b></div>
            <div class="pieza"><i class="bi bi-check2-square"></i><b>Seguimiento de tareas</b></div>
        </div>
    </div>
</section>

<!-- ===================== SECCIONES SHOWCASE ===================== -->
<section class="seccion" id="secciones" style="background:var(--gris-claro);">
    <div class="contenedor">
        <div class="centrado reveal">
            <span class="etiqueta"><i class="bi bi-grid-3x3-gap-fill"></i> Secciones</span>
            <h2 class="sec-titulo">Un espacio para cada parte de tu labor</h2>
            <p class="sec-sub">Organiza tu portafolio en cuatro secciones esenciales, ademas de
               la seguridad y el acceso que las acompanan.</p>
        </div>
        <div class="cards">
            <div class="card-x reveal"><div class="ic bg-indigo"><i class="bi bi-folder-fill"></i></div>
                <h3>Documentos</h3><p>Sube y resguarda PDF, Word e imagenes de tu labor docente.</p></div>
            <div class="card-x reveal d1"><div class="ic bg-naranja"><i class="bi bi-journal-text"></i></div>
                <h3>Notas</h3><p>Apuntes y recordatorios rapidos siempre a la mano.</p></div>
            <div class="card-x reveal d2"><div class="ic bg-cian"><i class="bi bi-easel2-fill"></i></div>
                <h3>Material didactico</h3><p>Recursos de ensenanza organizados por materia.</p></div>
            <div class="card-x reveal d3"><div class="ic bg-morado"><i class="bi bi-check2-square"></i></div>
                <h3>Tareas</h3><p>Da seguimiento a actividades, pendientes y entregas.</p></div>
            <div class="card-x reveal d1"><div class="ic bg-verde"><i class="bi bi-shield-check"></i></div>
                <h3>Seguridad</h3><p>Cada docente accede unicamente a su propio portafolio.</p></div>
            <div class="card-x reveal d2"><div class="ic bg-rosa"><i class="bi bi-cloud-arrow-up-fill"></i></div>
                <h3>Acceso en linea</h3><p>Consulta tu trabajo desde cualquier lugar y dispositivo.</p></div>
        </div>
    </div>
</section>

<!-- ===================== COMO FUNCIONA ===================== -->
<section class="seccion" id="como">
    <div class="contenedor">
        <div class="centrado reveal">
            <span class="etiqueta"><i class="bi bi-signpost-2-fill"></i> Como funciona</span>
            <h2 class="sec-titulo">Empieza en tres pasos</h2>
        </div>
        <div class="pasos">
            <div class="paso reveal"><div class="n">1</div><h3>Crea tu cuenta</h3>
                <p>Registrate con tu correo en menos de un minuto. Es gratis.</p></div>
            <div class="paso reveal d1"><div class="n">2</div><h3>Organiza tu portafolio</h3>
                <p>Agrega documentos, notas, material y tareas en sus secciones.</p></div>
            <div class="paso reveal d2"><div class="n">3</div><h3>Accede cuando quieras</h3>
                <p>Consulta y actualiza tu trabajo desde cualquier dispositivo.</p></div>
        </div>
    </div>
</section>

<!-- ===================== TESTIMONIOS ===================== -->
<section class="seccion" id="testimonios" style="background:var(--gris-claro);">
    <div class="contenedor">
        <div class="centrado reveal">
            <span class="etiqueta"><i class="bi bi-chat-quote-fill"></i> Testimonios</span>
            <h2 class="sec-titulo">Lo que dicen los docentes</h2>
        </div>
        <div class="testis">
            <div class="testi reveal">
                <div class="estrellas"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p>"Por fin tengo todo mi material en un solo lugar. Ya no pierdo tiempo buscando archivos entre memorias y correos."</p>
                <div class="autor"><span class="avatar bg-indigo">MG</span><div><b>Maria Gonzalez</b><span>Docente de Primaria</span></div></div>
            </div>
            <div class="testi reveal d1">
                <div class="estrellas"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p>"La interfaz es muy clara. Cualquier profesor puede usarla sin complicarse, aunque no sea experto en tecnologia."</p>
                <div class="autor"><span class="avatar bg-naranja">JR</span><div><b>Jorge Ramirez</b><span>Profesor de Secundaria</span></div></div>
            </div>
            <div class="testi reveal d2">
                <div class="estrellas"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p>"Me da tranquilidad saber que mi trabajo esta resguardado y que puedo consultarlo desde casa o la escuela."</p>
                <div class="autor"><span class="avatar bg-morado">LF</span><div><b>Laura Fernandez</b><span>Docente de Bachillerato</span></div></div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== CTA ===================== -->
<section class="seccion">
    <div class="contenedor">
        <div class="cta-band reveal">
            <h2>Comienza a organizar tu portafolio hoy</h2>
            <p>Crea tu cuenta gratuita y reune todo tu trabajo docente en un solo lugar.</p>
            <a class="btn btn-outline btn-lg" href="registro.php">Crear cuenta gratis <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- ===================== FOOTER ===================== -->
<footer class="footer">
    <div class="contenedor footer-grid">
        <div>
            <a class="marca" href="index.php"><img class="marca-img" src="img/logo.png" alt="<?= APP_NAME ?>"></a>
            <p><?= APP_DESC ?>. Organiza, resguarda y consulta tu trabajo docente desde un unico espacio seguro.</p>
        </div>
        <div>
            <h4>Navegacion</h4>
            <ul>
                <li><a href="#caracteristicas">Caracteristicas</a></li>
                <li><a href="#secciones">Secciones</a></li>
                <li><a href="#como">Como funciona</a></li>
                <li><a href="#testimonios">Testimonios</a></li>
            </ul>
        </div>
        <div>
            <h4>Cuenta</h4>
            <ul>
                <li><a href="login.php">Iniciar sesion</a></li>
                <li><a href="registro.php">Crear cuenta</a></li>
            </ul>
        </div>
        <div>
            <h4>Contacto</h4>
            <ul class="contacto">
                <li><i class="bi bi-envelope-fill"></i> contacto@edufolio.mx</li>
                <li><i class="bi bi-geo-alt-fill"></i> Mexico</li>
                <li><i class="bi bi-mortarboard-fill"></i> Proyecto academico 2026</li>
            </ul>
        </div>
    </div>
</footer>

<?php require __DIR__ . '/../app/layout/footer.php'; ?>
