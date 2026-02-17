<?php

require_once 'vendor/autoload.php';

use App\MyApp\AppInput;
use App\Decorators\DangerousHTMLTagsDecorator;
use App\Decorators\MarkdownDecorator;
use App\Decorators\PlainTextDecorator;
use App\Client\WebsiteClient;

echo "========== PATRÓN DECORATOR - SISTEMA DE FORMATEO DE TEXTO ==========";
echo "<br>";
echo "<br>";
echo "<br>";



// =============================================================================
//
//  ESCENARIO A: ADMINISTRADOR EDITA CONTENIDO DEL SITIO
//  - Administrador del sitio (confianza absoluta)
//  - Prioridad: Control total sobre HTML
//  - Solución: No aplicar ningún decorador
//  - Intervención: NULA (Se entrega el texto crudo)
//
// =============================================================================

echo "--- ESCENARIO A: ADMINISTRADOR DEL SITIO ---\n\n";

// Instanciamos el cliente que utilizará el sistema de formateo:
$websiteA = new WebsiteClient();

// HTML completo que escribe el administrador
$adminContent = <<<TEXT
<div class="hero">
  <h1>Bienvenido</h1>
  <script>initMap();</script>
  <button onclick="showContact()">Contacto</button>
</div>
TEXT;

// Instanciar SOLO el componente base (sin decoradores)
$processor = new AppInput();

// Procesar y mostrar resultado (se guarda tal cual, sin cambios)
echo "Entrada:\n$adminContent\n\n";
echo "Salida:\n";
$websiteA->displayContent($processor, $adminContent);
echo "\n\n";


// =============================================================================
//
//  ESCENARIO B: EDITOR DE CONTENIDO CONFIABLE
//  - Personal de edición verificado (confianza alta)
//  - Prioridad: Permitir HTML para diseño, pero evitar errores accidentales o scripts
//  - Solución: Solo filtrar etiquetas peligrosas (DangerousHTMLTagsDecorator)
//  - Intervención: BAJA (Solo limpia elementos de riesgo)
//
// =============================================================================

echo "--- ESCENARIO B: EDITOR DE CONTENIDO CONFIABLE ---\n\n";

// Instanciamos el cliente que utilizará el sistema de formateo:
$websiteB = new WebsiteClient();

// Texto con HTML de diseño y algún posible script accidental
$editorContent = <<<TEXT
<div class="p-4 bg-gray-100">
  <h2>Aviso Importante</h2>
  <p>El evento comenzará a las 10:00.</p>
  <script>alert('Esto es un script que el editor olvidó borrar');</script>
  <p>Atentamente, el equipo de redacción.</p>
</div>
TEXT;

// Instanciar el componente base
$textInput = new AppInput();

// Decorar solo con DangerousHTMLTagsDecorator
$processor = new DangerousHTMLTagsDecorator($textInput);

// Procesar y mostrar resultado
echo "Entrada:\n$editorContent\n\n";
echo "Salida:\n";
$websiteB->displayContent($processor, $editorContent);
echo "\n\n";


// =============================================================================
//
//  ESCENARIO C: MENSAJES PRIVADOS ENTRE USUARIOS PREMIUM
//  - Usuarios premium verificados (alta confianza)
//  - Prioridad: Formato rico profesional pero sencillo (Markdown)
//  - Solución: Solo aplicar formato (MarkdownDecorator)
//  - Intervención: MEDIA (Transforma sintaxis a HTML)
//
// =============================================================================

echo "--- ESCENARIO C: MENSAJES PRIVADOS ---\n\n";

// Instanciamos el cliente que utilizará el sistema de formateo:
$websiteC = new WebsiteClient();

// Texto que escribe el usuario premium (Markdown profesional)
$privateMessage = <<<TEXT
Hola Laura,

Te envío el **informe mensual**:

## Ventas Q1
- Producto A: 1,200 unidades
- Producto B: 800 unidades

Saludos,
Carlos
TEXT;

// Instanciar el componente base
$textInput = new AppInput();

// Decorar solo con MarkdownDecorator (sin filtros de seguridad adicionales)
$processor = new MarkdownDecorator($textInput);

// Procesar y mostrar resultado
echo "Entrada:\n$privateMessage\n\n";
echo "Salida:\n";
$websiteC->displayContent($processor, $privateMessage);
echo "\n\n";


// =============================================================================
//
//  ESCENARIO D: POSTS EN FORO DE LA COMUNIDAD
//  - Usuario registrado (confianza media)
//  - Prioridad: Formato bonito pero seguro
//  - Solución: MarkdownDecorator + DangerousHTMLTagsDecorator
//  - Intervención: ALTA (Transforma + Limpia)
//
// =============================================================================

echo "--- ESCENARIO D: POSTS DE FORO ---\n\n";

// Instanciamos el cliente que utilizará el sistema de formateo:
$websiteD = new WebsiteClient();

// Texto que escribe el usuario registrado (Markdown + posible HTML malicioso)
$forumPost = <<<TEXT
# Tutorial PHP

Aprender patrones es **esencial**.

<script>
console.log('Intento de ataque XSS detectado');
</script>
<b>Nota:</b> Use <i>siempre</i> decoradores.
TEXT;

// Instanciar el componente base
$textInput = new AppInput();

// Decorar primero con MarkdownDecorator
$withMarkdown = new MarkdownDecorator($textInput);

// Decorar después con DangerousHTMLTagsDecorator
$processor = new DangerousHTMLTagsDecorator($withMarkdown);

// Procesar y mostrar resultado
echo "Entrada:\n$forumPost\n\n";
echo "Salida:\n";
$websiteD->displayContent($processor, $forumPost);
echo "\n\n";


// =============================================================================
//
//  ESCENARIO E: COMENTARIOS PÚBLICOS (ANÓNIMOS)
//  - Usuario anónimo (confianza nula)
//  - Prioridad: Seguridad absoluta
//  - Solución: Eliminar TODO el HTML (PlainTextDecorator)
//  - Intervención: MÁXIMA (Elimina cualquier rastro de código)
//
// =============================================================================

echo "--- ESCENARIO E: COMENTARIOS PÚBLICOS ---\n\n";

// Instanciamos el cliente que utilizará el sistema de formateo:
$websiteE = new WebsiteClient();

// Texto que escribe el usuario anónimo (HTML + posible script malicioso)
$commentText = <<<TEXT
¡Gran artículo!
Visita <a href="http://spam.com">mi sitio</a>
<script>alert('Ataque XSS');</script>
Te doy **5 estrellas**
TEXT;

// Instanciar el componente base
$textInput = new AppInput();

// Decorar con PlainTextDecorator
$processor = new PlainTextDecorator($textInput);

// Procesar y mostrar resultado
echo "Entrada (Literal):\n";
echo htmlspecialchars($commentText);
echo "\n\n";
echo "Salida:\n";
$websiteE->displayContent($processor, $commentText);
echo "\n\n";


// =============================================================================
// RESUMEN
// =============================================================================

echo "========== RESUMEN DE COMPOSICIÓN ==========\n";
echo "Escenario A: AppInput (Base)\n";
echo "Escenario B: [ AppInput ] <- DangerousHTMLTagsDecorator\n";
echo "Escenario C: [ AppInput ] <- MarkdownDecorator\n";
echo "Escenario D: [ [ AppInput ] <- MarkdownDecorator ] <- DangerousHTMLTagsDecorator\n";
echo "Escenario E: [ AppInput ] <- PlainTextDecorator\n";
echo "============================================";
