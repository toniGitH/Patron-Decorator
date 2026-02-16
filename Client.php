<?php

require_once 'InputFormatInterface.php';

/**
 * CLIENTE
 * 
 * Esta función representa el código cliente que usa el sistema de formateo.
 * En una aplicación real, esto sería tu controlador, vista o template.
 * 
 * Recibe cualquier objeto que implemente InputFormat (componente base o decorado)
 * y llama a formatText() sin saber ni importarle cuántos decoradores hay apilados.
 */
function displayContent(InputFormatInterface $format, string $text)
{
    echo $format->formatText($text);
}
