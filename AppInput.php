<?php

require_once 'InputFormatInterface.php';

/**
 * ¿QUÉ REPRESENTA AppInput EN ESTE EJEMPLO?
 * 
 * Técnicamente: Es el "Portador del Texto Crudo".
 * 
 * En PHP, un simple string (`"Hola"`) no puede tener decoradores. No puedes "envolver" un string con una clase.
 * 
 * Por eso existe `AppInput`:
 * 1. Convierte el texto que escribió el usuario en un OBJETO.
 * 2. Al ser un objeto que implementa `InputFormatInterface`, ya permite que otros objetos 
 *    (los decoradores) se pongan encima de él.
 * 
 * ¿A quién se lo entrega?
 * El método `formatText` le "entrega" el texto al siguiente en la fila. 
 * - Si no hay decoradores, se lo entrega directamente al `echo` (la pantalla).
 * - Si hay decoradores, se lo entrega al primer decorador para que lo procese.
 * 
 * RESUMEN: Es la "Caja" donde metemos el texto original para que el patrón 
 * Decorator pueda empezar a trabajar. Sin esta caja (clase base), no habría 
 * nada que decorar.
 */
class AppInput implements InputFormatInterface
{
    public function formatText(string $text): string
    {
        // No hace nada, devuelve el texto original.
        // Es la base sobre la que se aplicarán todas las transformaciones.
        return $text;
    }
}
