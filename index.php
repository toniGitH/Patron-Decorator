<?php
// Incluimos la lógica central
require_once 'main.php';
// $scenarios ya está disponible gracias a main.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patrón Decorator - Visualizador Premium</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>Patrón Decorator</h1>
            <p class="subtitle">Estructura Dinámica de Formateo de Contenido</p>
        </header>

        <main class="scenarios-grid">
            <?php 
            $client = new \App\Client\WebsiteClient();
            $delay = 0;
            foreach ($scenarios as $key => $data): 
                $delay += 0.1;
            ?>
                <section class="card" style="animation-delay: <?= $delay ?>s">
                    <div class="card-header">
                        <div>
                            <span class="scenario-badge">Escenario <?= $key ?></span>
                            <h2 style="display:inline; margin-left:1rem; font-size:1.25rem; vertical-align:middle;"><?= $data['title'] ?></h2>
                        </div>
                        <div class="intervention-label">
                            Intervención: <span class="intervention-value"><?= $data['intervention'] ?></span>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Sección de Metadatos de Ancho Completo -->
                        <div class="scenario-full-meta" style="grid-column: 1 / -1; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border);">
                            <p style="font-size:0.95rem; color:var(--text-muted); margin-bottom:0.75rem;">
                                <strong>Situación:</strong> <span style="color:var(--text-main);"><?= $data['situation'] ?></span>
                            </p>
                            <p style="font-size:0.95rem; color:var(--text-muted); margin-bottom:0.75rem;">
                                <strong>Análisis del riesgo:</strong> <span style="color:var(--text-main);"><?= $data['risk_analysis'] ?></span>
                            </p>
                            <p style="font-size:0.95rem; color:var(--text-muted);">
                                <strong>Intervención:</strong> <span style="color:var(--text-main);"><?= $data['intervention_expl'] ?></span>
                            </p>
                        </div>

                        <!-- Columna Izquierda: Entrada -->
                        <div class="scenario-content-column">
                            <div class="section-title">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.478 1.647a.5.5 0 1 0-.956-.294l-4 13a.5.5 0 0 0 .956.294l4-13zM4.854 4.146a.5.5 0 0 1 0 .708L1.707 8l3.147 3.146a.5.5 0 0 1-.708.708l-3.5-3.5a.5.5 0 0 1 0-.708l3.5-3.5a.5.5 0 0 1 .708 0zm6.292 0a.5.5 0 0 0 0 .708L14.293 8l-3.147 3.146a.5.5 0 0 0 .708.708l3.5-3.5a.5.5 0 0 0 0-.708l-3.5-3.5a.5.5 0 0 0-.708 0z"/></svg>
                                Código escrito por el usuario (Raw)
                            </div>
                            <div class="content-box">
                                <pre class="raw-content"><?= htmlspecialchars($data['input']) ?></pre>
                            </div>
                        </div>

                        <!-- Columna Derecha: Resultado -->
                        <div class="scenario-content-column">
                            <div class="section-title">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/></svg>
                                Resultado final tras aplicar decoradores
                            </div>
                            <div class="content-box rendered-content">
                                <?php $client->displayContent($data['processor'], $data['input']); ?>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
        </main>

        <footer>
            <p>&copy; 2026 - Guía de Patrones Estructurales - Decorator</p>
        </footer>
    </div>
</body>
</html>
