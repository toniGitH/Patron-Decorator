# Entendiendo el Patrón Decorator

Este documento resume la lógica fundamental del patrón Decorator tal como se aplica en nuestro sistema de formateo de texto. 

---

## 1. El Requisito Obligatorio: `InputFormat` (El Contrato)
Todo sistema profesional necesita reglas. `InputFormat` es el **contrato legal** de nuestra aplicación. 

*   **¿Qué dice?**: Dictamina que cualquier "cosa" que el sistema entregue (ya sea para imprimir en pantalla o guardar en una base de datos) **debe** tener un método llamado `formatText()`.
*   **¿Cuál es el beneficio?**: No importa si el texto está crudo o ha pasado por diez filtros; el sistema siempre sabe que puede llamar a `formatText()` para obtener el resultado final.

## 2. El Punto de Partida: `TextInput` (El Núcleo)
En PHP, un `string` básico (`"Hola"`) no puede "envolverse" con clases ni tener decoradores. Necesitamos convertir ese texto en un objeto.

*   **¿Qué hace?**: Recibe un texto plano y lo devuelve tal cual, pero ahora **dentro de un objeto**.
*   **Decisión de Escenario**: Llegados a este punto, el sistema podría decidir no aplicar ningún filtro y retornar este objeto `TextInput` directamente. 
*   **Implementación Obligatoria**: Como cumple con el contrato, entrega el texto mediante el método `formatText()`, aunque en este caso no aplique ninguna transformación.
*   **Ejecución Garantizada**: El método `formatText()` de esta clase `TextInput` **SIEMPRE** se va a ejecutar. Ya sea como la "entrega final" de los datos al sistema o como el paso intermedio para ser capturado y procesado por un decorador. Es la puerta de salida única del objeto hacia el exterior.

## 3. La Infraestructura de Filtros: `TextFormat` (La Referencia Maestra)
Si queremos aplicar transformaciones, necesitamos una estructura que defina cómo se deben gestionar las transformaciones (o decoraciones) sobre el objeto anterior. Aquí entra `TextFormat`, que es una clase abstracta y genérica.

**Clase abstracta**: no define lógica de transformación propia. Es una referencia maestra donde deben mirarse los filtros concretos: los verdaderos filtros (como `PlainTextFilter` o `MarkdownFormat`) serán clases que heredarán de `TextFormat`, por lo que deberán cumplir las directrices y requisitos impuestos por esta clase abstracta.

**Doble requisito**:

    1.  **Cumplir el Contrato establecido por la interfaz**: debe implementar `InputFormat` porque su intención final es devolver algo procesado al sistema.
    2.  **Recibir y almacenar el objeto a transformar que se le envía desde el eslabón anterior**: Tiene la propiedad `protected $inputFormat` para poder recibir y almacenar el objeto (ya proceda del núcleo o de otro filtro) que queremos decorar.


## 4. Los Decoradores Concretos: Las Capas de la Cebolla
Aquí es donde realmente se "corta el bacalao". Los filtros (como `PlainTextFilter` o `MarkdownFormat`) son los que en la práctica envuelven al objeto `TextInput` y hacen el trabajo de transformación.

*   **Funcionamiento**: reciben el objeto en el constructor y lo almacenan. Cuando se solicita su método `formatText()`, ellos primero "piden" el texto al objeto que tienen guardado (mediante la llamada a `parent::formatText()`), luego aplican su propia transformación (el filtro) sobre ese texto recibido y finalmente devuelven el resultado ya transformado.


## La Cadena de Mando

Con este patrón, el flujo del programa viaja hacia adentro hasta el núcleo (`TextInput`) para obtener el texto original, y luego va avanzando hacia afuera pasando por cada filtro, capa, decorador o eslabón, donde cada uno "aporta su granito de arena" transformando el texto antes de pasárselo al siguiente hasta que "sale de la cebolla".

---

## Simplificando el patrón para entenderlo mejor

Si eliminamos las clases "contractuales" (`InputFormat` y `TextFormat`), el patrón se reduce a dos actores:
1.  **El Objeto Inicial TextInput**: que transforma el dato crudo en algo "decorable".
2.  **Los Decoradores (o filtros)**: que se aplican sobre ese objeto.

Las clases contractuales (`InputFormat` y `TextFormat`) son fundamentales para garantizar la **estabilidad, coherencia y ampliabilidad** del sistema. No son las que "ejecutan el corte del bacalao", pero son las que definen con qué cuchillo y de qué manera se debe cortar, asegurando que el sistema no se rompa al añadir nuevos filtros en el futuro.
