# Entendiendo el Patrón Decorator

Este documento resume la lógica fundamental del patrón Decorator tal como se aplica en nuestro ejemplo de sistema de formateo de texto.

👉🏼 [Volver al README](README.md)

---

## 1. El requisito obligatorio: `InputFormatInterface` (el contrato)

Todo sistema profesional necesita reglas. `InputFormatInterface` es el **contrato legal** de nuestra aplicación. 

*   **¿Qué dice?**: Dictamina que cualquier "cosa" que el sistema entregue (ya sea para imprimir en pantalla o guardar en una base de datos) **debe** tener un método llamado `formatText()`.

*   **¿Cuál es el beneficio?**: No importa la complejidad del procesamiento; el sistema solo sabe que tiene llamar a `formatText()` para obtener el resultado final.

## 2. El punto de partida: `AppInput` (el núcleo)

En PHP, un `string` básico no puede "envolverse" con clases. Necesitamos convertir ese texto en un objeto.

*   **Escenario sin decoradores**: es el uso más puro del núcleo. El sistema decide no aplicar ningún filtro y retorna este objeto directamente. Se entrega el texto **crudo**, con una intervención **NULA**.

*   **Implementación**: Entrega el texto mediante el método `formatText()`, sin aplicar ninguna transformación.

*   **Ejecución Garantizada**: Este método **SIEMPRE** se ejecuta, siendo la puerta de salida única del objeto hacia el exterior, ya sea como entrega final o paso intermedio.

## 3. La estructura de los decoradores: `AbstractDecorator` (la referencia maestra)

Para aplicar transformaciones dinámicas, necesitamos `AbstractDecorator`, una clase abstracta que define cómo se gestionan las capas sobre el objeto anterior.

*   **Doble Requisito**:

    1.  **Cumplir el Contrato**: Debe implementar `InputFormatInterface`.

    2.  **Almacenar el Eslabón Anterior**: Guarda en `$inputFormat` el objeto (núcleo u otro decorador) que está envolviendo.

## 4. Los decoradores concretos

Aquí es donde realmente se transforma el texto.

Cada uno de los decoradores aplica una determinada conversión al texto recibido:

### `DangerousHTMLTagsDecorator`

*   Podemos asociarlo al **escenario B (editor confiable)**: el usuario tiene permiso para usar HTML, pero queremos una red de seguridad que elimine scripts o atributos maliciosos de forma quirúrgica.

### `MarkdownDecorator`

*   Podemos asociarlo al **escenario C (mensajes privados)**: no se busca seguridad, sino utilidad. Transforma la sintaxis Markdown en etiquetas HTML, cambiando la estructura visual del texto.

### `PlainTextDecorator`

*   Podemos asociarlo al **escenario E (comentarios anónimos)**: el grado más radical de filtrado. Se elimina absolutamente cualquier etiqueta HTML, dejando solo el texto plano.

Y dado que en este patrón, los diferentes decoradores actúan como capas que pueden superponerse unas sobre otras, puede interesarnos aplicar más de un decorador:

### Composición: `MarkdownDecorator` + `DangerousHTMLTagsDecorator`

*   Podemos asociarlo al **escenario D (posts de foro)**: se combinan dos decoradores. Primero se transforma el Markdown y luego se sanea el resultado. Es un ejemplo perfecto de cómo el patrón permite apilar responsabilidades.

---

## 6. La ventaja del patrón Decorator

Esta arquitectura nos permite que el sistema sea **abierto a la extensión** (podemos añadir el `Escenario F` mañana mismo) pero **cerrado a la modificación** (no tenemos que tocar el código del Administrador para añadir filtros a los usuarios anónimos).

👉🏼 [Volver al README](README.md)