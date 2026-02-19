<?php

/**
 * LÓGICA CENTRAL DEL PATRÓN DECORATOR (ENFOQUE PEDAGÓGICO)
 * 
 * Este archivo contiene las instrucciones PHP puras para cada escenario.
 * El WebsiteClient ahora devuelve el texto procesado en lugar de imprimirlo,
 * lo que permite guardarlo en variables de forma limpia para la web.
 */

require_once 'vendor/autoload.php';

use App\MyApp\AppInput;
use App\Decorators\DangerousHTMLTagsDecorator;
use App\Decorators\MarkdownDecorator;
use App\Decorators\PlainTextDecorator;
use App\Client\WebsiteClient;

// 1. Instanciamos el cliente (El componente que se beneficia del patrón)
$client = new WebsiteClient();

// =============================================================================
//  EJECUCIÓN DE ESCENARIOS (Código Puro)
// =============================================================================

// --- ESCENARIO A: ADMINISTRADOR ---
// Un administrador de la web (confianza total), crea un determinado contenido.
// Como la confianza en el administrador es total, no se aplica NINGÚN filtro, corrección, cambio, ni nada.

    // 1.  Texto escrito por un administrador en un formulario interno:
$textoA = '<div class="hero"><h1>Bienvenido</h1><script>initMap();</script></div>';

    // 2. La aplicación crea una instancia de AppInput para recibir el contenido_
    //    - este paso es imprescindible, puesto que recibe un texto plano, que no puede ser decorado, y lo convierte en un objeto, que sí puede ser decorado.
    //    - este nuevo objeto, tiene un método llamado formatText(), que devuelve el contenido sin procesar. 
$entradaOriginalA = new AppInput(); 

    // 3. Seleccionamos el procesador que queremos usar:
    // - como no es necesario aplicar ningún filtro, el procesador a utilizar es el objeto nativo sin ningún envoltorio adicional.
$procesadorNativo = $entradaOriginalA;

    // 4. El cliente es el que orquesta la entrega del contenido final: 
    //    - el cliente llama a su método formatForDisplay()
    //    - ese método recibe el contenido original y el procesador a utilizar (cualquier objeto que implementa la interface InputFormatInterface)
    //    - ese método formatForDisplay() llama al método formatText() del procesador elegido, pasándole el contenido original, y retorna el resultado a la aplicación
$resultadoA = $client->formatForDisplay($procesadorNativo, $textoA);


// --- ESCENARIO B: EDITOR CONFIABLE ---
// Un editor de noticias oficial (confianza alta) escribe contenido.
// Se permite HTML para dar formato, pero se aplica un filtro de seguridad para eliminar riesgos accidentales (como scripts).

    // 1. Texto de noticias corporativas escrito por personal interno:
$textoB = '<h2>Aviso</h2><script>alert("XSS");</script><p>Contenido oficial</p>';

    // 2. Se crea la instancia de AppInput para convertir el texto plano en un objeto procesable:
$entradaOriginalB = new AppInput();

    // 3. Seleccionamos el procesador que queremos usar, envolviendo el objeto original:
    //    - el DangerousHTMLTagsDecorator "envuelve" al contenido original para añadirle la capacidad de saneamiento.
$procesadorDangerousHTML = new DangerousHTMLTagsDecorator($entradaOriginalB);

    // 4. El cliente orquesta la entrega del contenido final: 
    //    - el cliente llama a su método formatForDisplay()
    //    - ese método recibe el contenido original y
    //    - el procesador a utilizar (en este caso, el DangerousHTMLTagsDecorator, que es un objeto que implementa la interface InputFormatInterface)
    //    - ese método formatForDisplay() llama al método formatText() del procesador elegido, pasándole el contenido original, y retorna el resultado a la aplicación
$resultadoB = $client->formatForDisplay($procesadorDangerousHTML, $textoB);


// --- ESCENARIO C: USUARIO PREMIUM ---
// Un usuario con suscripción (confianza media-alta) envía un mensaje.
// Se le permite usar sintaxis Markdown para enriquecer el texto.

    // 1. Mensaje escrito por un usuario de pago en un campo de texto con soporte Markdown.
$textoC = "Hola **Laura**,\n\n## Ventas Q1\n- Producto A: 100";

    // 2. Instanciamos el soporte original para recibir el input del usuario.
$entradaOriginalC = new AppInput();

    // 3. Seleccionamos el procesador que queremos usar, envolviendo el objeto original:
    //    - el MarkdownDecorator "envuelve" al contenido original para añadirle la capacidad de conversión de sintáxis.
    //    - este envoltorio dota al objeto de la funcionalidad adicional de convertir sintaxis especial en HTML.
$procesadorMarkdown = new MarkdownDecorator($entradaOriginalC);

    // 4. El cliente orquesta la entrega del contenido final: 
    //    - el cliente llama a su método formatForDisplay()
    //    - ese método recibe el contenido original y
    //    - el procesador a utilizar (en este caso, el MarkdownDecorator, que es un objeto que implementa la interface InputFormatInterface)
    //    - ese método formatForDisplay() llama al método formatText() del procesador elegido, pasándole el contenido original, y retorna el resultado a la aplicación
$resultadoC = $client->formatForDisplay($procesadorMarkdown, $textoC);


// --- ESCENARIO D: POST EN FORO ---
// Un usuario registrado publica una guía.
// Es una zona pública, por lo que se quiere permitir Markdown pero con una seguridad estricta contra ataques XSS.
// En este escenario se hace necesario aplicar 2 decoradores

    // 1. Guía escrita por un usuario registrado en el foro. Requiere saneamiento y Markdown.
$textoD = "# Tutorial PHP\n**Esencial**\n<script>hack_attempt();</script>";

    // 2. Se crea el objeto primigenio que recibirá el texto original del formulario.
$entradaOriginalD = new AppInput();

    // 3. Seleccionamos el procesador aplicando una "doble envoltura":
    // Primero, envolvemos el contenido original con el decorador de Markdown para permitir formato rico.
$procesadorMarkdown = new MarkdownDecorator($entradaOriginalD);
    // Segundo, envolvemos lo anterior con el decorador de seguridad para garantizar que el HTML resultante sea seguro.
$procesadorDangerousHTML = new DangerousHTMLTagsDecorator($procesadorMarkdown);

    // 4. El cliente orquesta la entrega del contenido final:
    //    - el cliente llama a su método formatForDisplay(), pasándole el texto y el último decorador de la cadena ($procesadorDangerousHTML).
    //    - la llamada formatText() se propaga en cascada hacia el "núcleo":
    //       a) el DangerousHTMLTagsDecorator llama a formatText() del objeto que envuelve (el MarkdownDecorator).
    //       b) el MarkdownDecorator llama a formatText() del objeto que envuelve (el núcleo AppInput).
    //       c) el núcleo AppInput simplemente retorna el texto original sin cambios.
    //    - tras llegar al fondo, la respuesta "vuelve" hacia afuera aplicándose las transformaciones:
    //       d) el MarkdownDecorator recibe el texto original, lo convierte a HTML y lo devuelve al nivel superior.
    //       e) El DangerousHTMLTagsDecorator recibe ese HTML, filtra las etiquetas peligrosas y devuelve el resultado final al cliente.
$resultadoD = $client->formatForDisplay($procesadorDangerousHTML, $textoD);


// --- ESCENARIO E: COMENTARIO ANÓNIMO ---
// Un visitante desconocido (confianza nula) escribe un comentario.
// No se quiere permitir ningún tipo de etiqueta HTML por seguridad absoluta.

    // 1. Opinión escrita por un visitante desconocido.
$textoE = "¡Hola! <a href='http://spam.com'>Spam</a><script>xss();</script>";

    // 2. Instanciamos el punto de entrada original para el comentario.
$entradaOriginalE = new AppInput();

    // 3. Seleccionamos el procesador que queremos usar, envolviendo el objeto original:
    //    - el PlainTextDecorator "envuelve" al contenido original para eliminar cualquier formato.
    //    - este envoltorio dota al objeto de la funcionalidad adicional de eliminar cualquier formato.
$procesadorE = new PlainTextDecorator($entradaOriginalE);

    // 4. El cliente orquesta la entrega del contenido final: 
    //    - el cliente llama a su método formatForDisplay()
    //    - ese método recibe el contenido original y
    //    - el procesador a utilizar (en este caso, el PlainTextDecorator, que es un objeto que implementa la interface InputFormatInterface)
    //    - ese método formatForDisplay() llama al método formatText() del procesador elegido, pasándole el contenido original, y retorna el resultado a la aplicación, asegurando que solo se visualice texto inofensivo.
$resultadoE = $client->formatForDisplay($procesadorE, $textoE);


// ===============================================================
//  METADATA PARA LA VISUALIZACIÓN WEB - NO FORMA PARTE DEL PATRÓN
// ===============================================================

$scenariosData = [
    'A' => [
        'title' => 'Administrador del Sitio',
        'situation' => 'El administrador crea o edita contenido estructural de la web.',
        'risk_analysis' => 'Confianza absoluta. Editor confiable. No debería haber riesgos.',
        'intervention' => 'NULA',
        'intervention_expl' => 'Ninguna. Se entrega el texto crudo tal cual se escribió.',
        'input' => $textoA,
        'output' => $resultadoA,
        'php_code' => '$entradaOriginal = new AppInput();' . "\n" . '$resultado = $client->formatForDisplay($entradaOriginal, $texto);'
    ],
    'B' => [
        'title' => 'Editor de Contenido Confiable',
        'situation' => 'Personal de la empresa publica noticias o avisos oficiales.',
        'risk_analysis' => 'Confianza alta. Editor confiable. Riesgo bajo.',
        'intervention' => 'BAJA',
        'intervention_expl' => 'Medidas prefentivas. Se eliminan solo scripts y atributos de riesgo.',
        'input' => $textoB,
        'output' => $resultadoB,
        'php_code' => '$entradaOriginal = new AppInput();' . "\n" . '$procesador = new DangerousHTMLTagsDecorator($entradaOriginal);' . "\n" . '$resultado = $client->formatForDisplay($procesador, $texto);'
    ],
    'C' => [
        'title' => 'Mensajes Privados Premium',
        'situation' => 'Mensajería privada entre usuarios con suscripción de pago.',
        'risk_analysis' => 'Confianza alta. Contendio no visualizable por toda la comunidad. Riesgo bajo.',
        'intervention' => 'MEDIA',
        'intervention_expl' => 'Transformación. Sólo se convierte la sintaxis Markdown a HTML.',
        'input' => $textoC,
        'output' => $resultadoC,
        'php_code' => '$entradaOriginal = new AppInput();' . "\n" . '$procesador = new MarkdownDecorator($entradaOriginal);' . "\n" . '$resultado = $client->formatForDisplay($procesador, $texto);'
    ],
    'D' => [
        'title' => 'Posts de Foro de la Comunidad',
        'situation' => 'Un usuario registrado publica contenido en el foro público.',
        'risk_analysis' => 'Confianza baja. Contenido visualizable por toda la comunidad. Riesgo alto.',
        'intervention' => 'ALTA',
        'intervention_expl' => 'Combinada. Conversión de Markdown y saneamiento de etiquetas y scripts peligrosos.',
        'input' => $textoD,
        'output' => $resultadoD,
        'php_code' => '$entradaOriginal = new AppInput();' . "\n" . '$conMarkdown = new MarkdownDecorator($entradaOriginal);' . "\n" . '$conSeguridad = new DangerousHTMLTagsDecorator($conMarkdown);' . "\n" . '$resultado = $client->formatForDisplay($conSeguridad, $texto);'
    ],
    'E' => [
        'title' => 'Comentarios Públicos (Anónimos)',
        'situation' => 'Un visitante sin cuenta escribe su opinión en un post.',
        'risk_analysis' => 'Confianza nula. Contenido visualizable por toda la comunidad. Riesgo muy alto.',
        'intervention' => 'MÁXIMA',
        'intervention_expl' => 'Drástica. Se elimina cualquier rastro de HTML.',
        'input' => $textoE,
        'output' => $resultadoE,
        'php_code' => '$entradaOriginal = new AppInput();' . "\n" . '$procesador = new PlainTextDecorator($entradaOriginal);' . "\n" . '$resultado = $client->formatForDisplay($procesador, $texto);'
    ]
];
