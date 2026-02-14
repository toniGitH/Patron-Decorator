<?php

namespace RefactoringGuru\Decorator\RealWorld;

require_once 'InputFormat.php';

/**
 * Componente Concreto Base: devuelve el texto tal cual, sin procesar.
 * 
 * PROPÓSITO: Es el "núcleo" sobre el que se aplicarán los decoradores.
 * No hace ningún filtrado, formateo, o transformación.
 * 
 * ¿Por qué existe?
 * - Es el punto de partida de la cadena de decoración
 * - Representa "texto sin procesar"
 * - Permite tener un objeto base que cumple la interfaz
 * 
 * Analogía: Es como el lienzo en blanco antes de pintar.
 */
class TextInput implements InputFormat
{
    public function formatText(string $text): string
    {
        // No hace nada, devuelve el texto original
        return $text;
    }
}
