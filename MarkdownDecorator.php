<?php

require_once 'AbstractDecorator.php';

/**
 * Decorador Concreto: Convierte sintaxis Markdown a HTML.
 * 
 * USO: Para posts de foro donde permites escribir en Markdown
 * (más fácil y seguro que HTML directo).
 * 
 * RESPONSABILIDAD: Transformación de formato.
 * Esta responsabilidad NO pertenece al componente base.
 * 
 * Convierte:
 * - # Título → <h1>Título</h1>
 * - **negrita** → <strong>negrita</strong>
 * - *cursiva* → <em>cursiva</em>
 * - Párrafos → <p>...</p>
 * 
 * IMPORTANTE: Este decorador GENERA HTML, por lo que típicamente
 * se usa ANTES de un filtro de HTML peligroso en la cadena.
 */
class MarkdownDecorator extends AbstractDecorator
{
    public function formatText(string $text): string
    {
        // PRIMERO: obtener el texto procesado por el decorador anterior
        $text = parent::formatText($text);

        // DESPUÉS: aplicar conversión de Markdown a HTML

        // === Formatear elementos de bloque ===
        $chunks = preg_split('|\n\n|', $text);
        foreach ($chunks as &$chunk) {
            // Formatear encabezados: # Título → <h1>Título</h1>
            if (preg_match('|^#+|', $chunk)) {
                $chunk = preg_replace_callback('|^(#+)(.*?)$|', function ($matches) {
                    $h = strlen($matches[1]); // Número de # determina el nivel
                    return "<h$h>" . trim($matches[2]) . "</h$h>";
                }, $chunk);
            } 
            // Formatear párrafos
            else {
                $chunk = "<p>$chunk</p>";
            }
        }
        $text = implode("\n\n", $chunks);

        // === Formatear elementos inline ===
        $text = preg_replace("|__(.*?)__|", '<strong>$1</strong>', $text);     // __texto__
        $text = preg_replace("|\*\*(.*?)\*\*|", '<strong>$1</strong>', $text); // **texto**
        $text = preg_replace("|_(.*?)_|", '<em>$1</em>', $text);               // _texto_
        $text = preg_replace("|\*(.*?)\*|", '<em>$1</em>', $text);             // *texto*

        return $text;
    }
}
