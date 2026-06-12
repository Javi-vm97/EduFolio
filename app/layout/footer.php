<?php
/* EduFolio - Pie HTML comun. Variables: $vista_app (bool), $ocultar_pie (bool) para landing con pie propio. */
$vista_app   = $vista_app ?? false;
$ocultar_pie = $ocultar_pie ?? false;
?>
<?php if ($vista_app): ?>
    </main><!-- .contenido -->
</div><!-- .layout-app -->
<?php endif; ?>

<?php if (!$ocultar_pie && !$vista_app): ?>
<footer class="pie-global">
    <span>&copy; <?= date('Y') ?> <?= APP_NAME ?> — <?= APP_DESC ?></span>
</footer>
<?php endif; ?>

<script src="assets/js/app.js"></script>
</body>
</html>
