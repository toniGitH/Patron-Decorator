<?php

namespace App\Decorators;

use App\MyApp\InputFormatInterface;

/**
 * Clase Base Abstracta para todos los decoradores.
 * 
 * PROPÓSITO: Proporcionar la infraestructura común de decoración.
 * 
 * ¿Qué hace?
 * 1. Implementa InputFormat (para ser compatible con la interfaz)
 * 2. Guarda una referencia al objeto que está decorando ($inputFormat)
 * 3. Delega la llamada al objeto decorado por defecto
 * 
 * ¿Por qué clase abstracta y no interfaz?
 * - Porque TODOS los decoradores necesitan:
 *   * Una propiedad para guardar el objeto decorado
 *   * Un constructor que reciba ese objeto
 *   * Un método que delegue por defecto
 * - Esto evita duplicar código en cada decorador concreto
 * 
 * Los decoradores CONCRETOS heredarán de esta clase y SOBRESCRIBIRÁN
 * formatText() para añadir su lógica específica ANTES y/o DESPUÉS
 * de llamar a parent::formatText().
 */
abstract class AbstractDecorator implements InputFormatInterface
{
    /**
     * El formateador que estamos "envolviendo" o "decorando"
     * Puede ser:
     * - Un AppInput (componente base)
     * - Otro decorador (permitiendo apilar decoradores)
     */
    protected $inputFormat;

    public function __construct(InputFormatInterface $inputFormat)
    {
        $this->inputFormat = $inputFormat;
    }

    /**
     * Por defecto, simplemente delega al formateador envuelto.
     * Los decoradores concretos sobrescribirán este método.
     */
    public function formatText(string $text): string
    {
        return $this->inputFormat->formatText($text);
    }
}
