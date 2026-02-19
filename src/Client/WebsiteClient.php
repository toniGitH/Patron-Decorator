<?php

namespace App\Client;

use App\MyApp\InputFormatInterface;

/**
 * CLIENTE
 * 
 * Esta función representa el código cliente que usa el sistema de formateo.
 * En una aplicación real, esto sería tu controlador, vista o template.
 * 
 * Recibe cualquier objeto que implemente InputFormat (componente base o decorado)
 * y llama a formatText() sin saber ni importarle cuántos decoradores hay apilados.
 */

class WebsiteClient
{
    /**
     * Procesa y devuelve el contenido utilizando el formato proporcionado.
     * El cliente es el que "utiliza" la estructura de decoradores.
     */
    public function formatForDisplay(InputFormatInterface $format, string $text): string
    {
        return $format->formatText($text);
    }
}
