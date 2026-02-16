# Entendiendo el Patrón Decorator

Este documento resume la lógica fundamental del patrón Decorator tal como se aplica en nuestro sistema de formateo de texto, ordenado de menor a mayor grado de transformación del texto original.

---

## 1. El Requisito Obligatorio: `InputFormatInterface` (El Contrato)
Todo sistema profesional necesita reglas. `InputFormatInterface` es el **contrato legal** de nuestra aplicación. 

*   **¿Qué dice?**: Dictamina que cualquier "cosa" que el sistema entregue (ya sea para imprimir en pantalla o guardar en una base de datos) **debe** tener un método llamado `formatText()`.
*   **¿Cuál es el beneficio?**: No importa la complejidad del procesamiento; el sistema siempre sabe que puede llamar a `formatText()` para obtener el resultado final.

## 2. El Punto de Partida: `AppInput` (El Núcleo)
En PHP, un `string` básico no puede "envolverse" con clases. Necesitamos convertir ese texto en un objeto.

*   **Escenario A (Administrador)**: Es el uso más puro del núcleo. El sistema decide no aplicar ningún filtro y retorna este objeto directamente. Se entrega el texto **crudo**, con una intervención **NULA**.
*   **Implementación**: Entrega el texto mediante el método `formatText()`, sin aplicar ninguna transformación.
*   **Ejecución Garantizada**: Este método **SIEMPRE** se ejecuta, siendo la puerta de salida única del objeto hacia el exterior, ya sea como entrega final o paso intermedio.

## 3. La Infraestructura de Filtros: `AbstractDecorator` (La Referencia Maestra)
Para aplicar transformaciones dinámicas, necesitamos `AbstractDecorator`, una clase abstracta que define cómo se gestionan las capas sobre el objeto anterior.

*   **Doble Requisito**:
    1.  **Cumplir el Contrato**: Debe implementar `InputFormatInterface`.
    2.  **Almacenar el Eslabón Anterior**: Guarda en `$inputFormat` el objeto (núcleo u otro decorador) que está envolviendo.

## 4. Los Decoradores Concretos: Grados de Intervención
Aquí es donde realmente se transforma el texto. Los hemos ordenado según cuánto alteran el mensaje original:

### Intervención BAJA: `DangerousHTMLTagsDecorator`
*   **Escenario B (Editor Confiable)**: El usuario tiene permiso para usar HTML, pero queremos una red de seguridad que elimine scripts o atributos maliciosos de forma quirúrgica.

### Intervención MEDIA: `MarkdownDecorator`
*   **Escenario C (Mensajes Privados)**: No se busca seguridad, sino utilidad. Transforma la sintaxis Markdown en etiquetas HTML, cambiando la estructura visual del texto.

### Intervención ALTA: Composición (Markdown + DangerousTags)
*   **Escenario D (Posts de Foro)**: Se combinan dos decoradores. Primero se transforma el Markdown y luego se sanea el resultado. Es un ejemplo perfecto de cómo el patrón permite apilar responsabilidades.

### Intervención MÁXIMA: `PlainTextDecorator`
*   **Escenario E (Comentarios Anónimos)**: El grado más radical de filtrado. Se elimina absolutamente cualquier etiqueta HTML, dejando solo el texto plano.

---

## Conclusión Ejecutiva

Si eliminamos las clases "contractuales" (`InputFormatInterface` y `AbstractDecorator`), el patrón se reduce a:
1.  **El Objeto Inicial**: Que transforma el dato crudo en algo "decorable".
2.  **Las Capas/Filtros**: Que se aplican sobre ese objeto según la necesidad del contexto.

Esta arquitectura nos permite que el sistema sea **abierto a la extensión** (podemos añadir el `Escenario F` mañana mismo) pero **cerrado a la modificación** (no tenemos que tocar el código del Administrador para añadir filtros a los usuarios anónimos).
