<?php

require_once 'AbstractDecorator.php';

/**
 * Decorador Concreto: Elimina TODAS las etiquetas HTML del texto.
 * 
 * USO: Para comentarios donde NO quieres permitir ningún HTML.
 * 
 * RESPONSABILIDAD: Filtrado de seguridad extremo.
 * Esta responsabilidad NO pertenece al componente base (TextInput).
 * 
 * ¿Cómo funciona?
 * 1. Llama a parent::formatText($text) 
 *    → Esto ejecuta el formateo del objeto que está decorando
 * 2. Toma el resultado
 * 3. Le aplica strip_tags() para eliminar TODO el HTML
 * 4. Devuelve el resultado limpio
 */
class PlainTextDecorator extends AbstractDecorator
{
    public function formatText(string $text): string
    {
        // PRIMERO: obtener el texto procesado por el decorador anterior
        $text = parent::formatText($text);
        
        // SEGUNDO: Eliminar bloques <script> completos (incluido el contenido)
        // Usamos la misma lógica que en DangerousHTMLTagsFilter para que no quede rastro del código
        $text = preg_replace("|<script.*?>([\s\S]*)?</script>|i", '', $text);

        // TERCERO: aplicar strip_tags() para eliminar cualquier otra etiqueta que quede
        return strip_tags($text);
    }
}
