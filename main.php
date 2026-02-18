<?php

/**
 * LÓGICA CENTRAL DEL PATRÓN DECORATOR
 * 
 * Este archivo contiene la configuración de los 5 escenarios.
 * Puede ser ejecutado directamente por consola: php main.php
 * O ser incluido por index.php para la visualización web.
 */

require_once 'vendor/autoload.php';

use App\MyApp\AppInput;
use App\Decorators\DangerousHTMLTagsDecorator;
use App\Decorators\MarkdownDecorator;
use App\Decorators\PlainTextDecorator;
use App\Client\WebsiteClient;

// Estructura para almacenar los resultados y compartirlos con la web
$scenarios = [];

// =============================================================================
//  CONFIGURACIÓN DE ESCENARIOS
// =============================================================================

// Escenario A
$contentA = <<<TEXT
<div class="hero">
  <h1>Bienvenido</h1>
  <script>initMap();</script>
  <button onclick="showContact()">Contacto</button>
</div>
TEXT;
$scenarios['A'] = [
    'title' => 'Administrador del Sitio',
    'risk_analysis' => 'Confianza absoluta. Control total sobre HTML.',
    'situation' => 'El administrador crea o edita contenido estructural de la web.',
    'intervention' => 'NULA',
    'intervention_expl' => 'Ninguna. Se entrega el texto crudo tal cual se escribió.',
    'input' => $contentA,
    'processor' => new AppInput()
];

// Escenario B
$contentB = <<<TEXT
<div class="p-4 bg-gray-100">
  <h2>Aviso Importante</h2>
  <p>El evento comenzará a las 10:00.</p>
  <script>alert('Script accidental');</script>
  <p>Atentamente, el equipo.</p>
</div>
TEXT;
$scenarios['B'] = [
    'title' => 'Editor de Contenido Confiable',
    'risk_analysis' => 'Confianza alta. Permite HTML pero sanea riesgos.',
    'situation' => 'Personal de la empresa publica noticias o avisos oficiales.',
    'intervention' => 'BAJA',
    'intervention_expl' => 'Selectiva. Se eliminan solo scripts y atributos de riesgo (onclick, etc).',
    'input' => $contentB,
    'processor' => new DangerousHTMLTagsDecorator(new AppInput())
];

// Escenario C
$contentC = <<<TEXT
Hola Laura,

Te envío el **informe mensual**:

## Ventas Q1
- Producto A: 1,200 unidades
- Producto B: 800 unidades

Saludos, Carlos
TEXT;
$scenarios['C'] = [
    'title' => 'Mensajes Privados Premium',
    'risk_analysis' => 'Alta confianza. Formato Markdown rico.',
    'situation' => 'Mensajería privada entre usuarios con suscripción de pago.',
    'intervention' => 'MEDIA',
    'intervention_expl' => 'Transformación. Se convierte la sintaxis Markdown a etiquetas HTML.',
    'input' => $contentC,
    'processor' => new MarkdownDecorator(new AppInput())
];

// Escenario D
$contentD = <<<TEXT
# Tutorial PHP

Aprender patrones es **esencial**.

<script>
console.log('Intento de ataque XSS');
</script>
<b>Nota:</b> Use <i>siempre</i> decoradores.
TEXT;
$scenarios['D'] = [
    'title' => 'Posts de Foro de la Comunidad',
    'risk_analysis' => 'Confianza media. Transforma y Sanear.',
    'situation' => 'Un usuario registrado publica una guía o pregunta en el foro público.',
    'intervention' => 'ALTA',
    'intervention_expl' => 'Combinada. Se convierte Markdown y luego se sanea el HTML resultante.',
    'input' => $contentD,
    'processor' => new DangerousHTMLTagsDecorator(new MarkdownDecorator(new AppInput()))
];

// Escenario E
$contentE = <<<TEXT
¡Gran artículo!
Visita <a href="http://spam.com">mi sitio</a>
<script>alert('Ataque XSS');</script>
Te doy **5 estrellas**
TEXT;
$scenarios['E'] = [
    'title' => 'Comentarios Públicos (Anónimos)',
    'risk_analysis' => 'Confianza nula. Seguridad absoluta.',
    'situation' => 'Un visitante sin cuenta escribe su opinión en un post del blog.',
    'intervention' => 'MÁXIMA',
    'intervention_expl' => 'Drástica. Se elimina cualquier rastro de HTML, dejando solo texto plano.',
    'input' => $contentE,
    'processor' => new PlainTextDecorator(new AppInput())
];

// El bloque CLI ha sido eliminado para simplificar el código y centrarse en la visualización web.
