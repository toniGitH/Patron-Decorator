<?php

require_once 'AbstractDecorator.php';

/**
 * Decorador Concreto: Elimina solo las etiquetas HTML PELIGROSAS.
 * 
 * USO: Para posts de foro donde QUIERES permitir HTML básico (negrita, cursiva)
 * pero NO quieres permitir scripts que puedan ejecutar ataques XSS.
 * 
 * RESPONSABILIDAD: Filtrado de seguridad selectivo.
 * Esta responsabilidad NO pertenece al componente base.
 * 
 * ¿Qué filtra?
 * - Tags <script>
 * - Atributos peligrosos como onclick, onkeypress (que ejecutan JavaScript)
 * 
 * ¿Qué NO filtra?
 * - HTML seguro como <b>, <i>, <p>, <a> (sin atributos peligrosos)
 */
class DangerousHTMLTagsDecorator extends AbstractDecorator
{
    /**
     * Patrones de etiquetas HTML peligrosas
     */
    private $dangerousTagPatterns = [
        "|<script.*?>([\s\S]*)?</script>|i", // Elimina <script>...</script>
    ];

    /**
     * Atributos HTML que pueden ejecutar JavaScript
     */
    private $dangerousAttributes = [
        "onclick", 
        "onkeypress",
        // En producción añadirías: onload, onerror, onmouseover, etc.
    ];

    public function formatText(string $text): string
    {
        // PRIMERO: obtener el texto procesado por el decorador anterior
        $text = parent::formatText($text);

        // DESPUÉS: aplicar nuestro filtrado

        // 1. Eliminar tags peligrosos completos (como <script>)
        foreach ($this->dangerousTagPatterns as $pattern) {
            $text = preg_replace($pattern, '', $text);
        }

        // 2. Eliminar atributos peligrosos de los tags que quedan
        foreach ($this->dangerousAttributes as $attribute) {
            $text = preg_replace_callback('|<(.*?)>|', function ($matches) use ($attribute) {
                $result = preg_replace("|$attribute=|i", '', $matches[1]);
                return "<" . $result . ">";
            }, $text);
        }

        return $text;
    }
}
