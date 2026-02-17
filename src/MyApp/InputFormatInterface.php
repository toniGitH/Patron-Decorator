<?php

namespace App\MyApp;

/**
 * Interfaz que define el contrato para formatear texto.
 * 
 * CONTEXTO: Estamos construyendo un sistema de comentarios/posts para un sitio web.
 * Sabemos desde el INICIO que necesitaremos diferentes tipos de procesamiento de texto:
 * - Texto plano (sin procesar)
 * - Filtrado de HTML peligroso
 * - Conversión de Markdown
 * - Eliminación total de tags
 * 
 * Esta interfaz existe desde el principio porque sabemos que habrá múltiples
 * formas de procesar texto que necesitarán COMBINARSE.
 */
interface InputFormatInterface
{
    /**
     * Formatea/procesa un texto y devuelve el resultado.
     */
    public function formatText(string $text): string;
}
