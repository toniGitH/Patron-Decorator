# Definición Formal: El Patrón Decorator

El patrón **Decorator** es un patrón estructural que permite añadir funcionalidades o responsabilidades a **instancias (objetos)** de una clase base, evitando cargarlas con lógicas opcionales que no forman parte de su naturaleza esencial. Para lograrlo, el patrón propone la creación de una **Interface Común** que debe ser implementada tanto por esa clase base como por todas aquellas clases (llamadas **decoradores**) que sirvan para añadir esas funcionalidades o responsabilidades adicionales. Con la finalidad **de** reutilizar código y estandarizar las clases decoradores, éstas deberán extender de una **clase abstracta** que actúa de "modelo" para construir dichas clases.

Este patrón propone una arquitectura basada en los siguientes elementos clave:

1.  **Interface Común (`InputFormatInterface`)**: Es el contrato que estandariza la forma de uso tanto del objeto inicial como de todos sus decoradores. Gracias a ella, la aplicación cliente depende de una **abstracción y no de concreciones**, desconociendo si está llamando al objeto base o a un decorador, lo que le permite funcionar sin necesidad de conocer la complejidad interna de la cadena.

2.  **Componente Concreto (ej. `AppInput`)**: Es la clase que representa el objeto original al que queremos añadir capacidades. Dependiendo del contexto, este componente puede ser una clase con lógica compleja preexistente (como un sistema de envío de emails) o, como en nuestro ejemplo, una clase creada específicamente para **convertir un dato primitivo (un string) en un objeto**. En este último caso, su función es vital para "objetivizar" el contenido, permitiendo que el patrón pueda empezar a trabajar sobre una instancia que rinda cuentas a la interfaz común.

3.  **Clase Abstracta Decoradora (`AbstractDecorator`)**: Actúa como el modelo para todos los decoradores. Su función es proporcionar una **propiedad específica destinada a almacenar la referencia** al objeto que se va a decorar (ya sea el núcleo original o un objeto que ya ha sido decorado previamente), facilitando así la delegación de llamadas hacia el interior de la cadena.

4.  **Decoradores Concretos**: Son las clases (como `MarkdownDecorator`, `DangerousHTMLTagsDecorator` o `PlainTextDecorator`) que heredan del modelo abstracto para inyectar una lógica específica de filtrado, limpieza o transformación, antes o después de pasar la ejecución al siguiente elemento.

En resumen, el patrón Decorator permite que una aplicación construya procesos complejos y modulares "apilando" envoltorios sobre un objeto base, donde cada capa añade una capacidad única sin que el cliente (el componente que usa el objeto) necesite conocer la complejidad de la cadena de procesamiento.

---

## Anexo: Casuística y Aplicación Real

Existen dos escenarios principales donde el patrón Decorator demuestra su utilidad, cada uno con un enfoque pedagógico y técnico distinto:

1.  **Ampliación de Funcionalidades Existentes (Caso de Negocio)**: Es el uso más habitual en aplicaciones de alto nivel. Se aplica sobre clases que ya tienen una responsabilidad clara (ej. enviar notificaciones, calcular impuestos) para añadir capas opcionales de lógica sin sobrecargar la clase original ni crear jerarquías de herencia rígidas o infinitas variaciones.

2.  **"Objetivización" de Tipos Primitivos (Caso de Infraestructura)**: Es común en el desarrollo de librerías y flujos de datos (ej. Java Streams). Aquí, el patrón comienza creando un objeto base cuya única misión es envolver un dato simple (como un string o un chorro de bytes) para convertirlo en un elemento "decorable" y permitir un procesamiento modular en cadena.

Mientras que el primer caso es más frecuente en el día a día del programador de aplicaciones, el segundo es arquitectónicamente más puro para fines educativos, ya que permite aislar la estructura del patrón de la complejidad de la lógica de negocio, facilitando su comprensión conceptual.
