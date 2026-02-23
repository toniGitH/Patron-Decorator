<a name="top"></a>

# 🪆 El patrón Decorator - Guía Completa

Repositorio creado para explicar el patrón **Decorator** y su implementación mediante un ejemplo práctico en **PHP** (Conversor de texto para web).

<br>

## 📖 Tabla de contenidos

<details>
  <summary>Mostrar contenidos</summary>
  <br>
  <ul>
    <li>🪆 <a href="#-el-patrón-decorator">El patrón Decorator</a>
      <ul>
        <li>🛂 <a href="#-elementos-típicos-que-encontramos-en-un-patrón-decorator">Elementos típicos que encontramos en un patrón Decorator</a></li>
        <li>👍🏼 <a href="#-cuándo-usar-el-patrón-decorator">¿Cuándo usar el patrón Decorator?</a></li>
        <li>🎯 <a href="#-principales-beneficios-de-aplicar-el-patrón-decorator">Principales beneficios de aplicar el patrón Decorator</a></li>
      </ul>
    </li>
    <li>🧪 <a href="#-ejemplo-de-implementación-sistema-de-formateo-de-contenido-para-web">Ejemplo de implementación: Sistema de formateo de contenido para web</a>
      <ul>
        <li>🔎 <a href="#-explicacion-detallada-del-ejemplo">Explicacion detallada del ejemplo</a></li>
        <li>🤔 <a href="#-entendiendo-el-patrón-decorator">Entendiendo el patrón Decorator</a></li>
        <li>👉🏼 <a href="#-identificación-de-los-principales-archivos-del-ejemplo">Identificación de los principales archivos del ejemplo</a></li>
      </ul>
    </li>
    <li>📂 <a href="#-estructura-del-proyecto-y-composer">Estructura del Proyecto y Composer</a></li>
    <li>📋 <a href="#-requisitos">Requisitos</a></li>
    <li>🚀 <a href="#-instalación-y-ejecución">Instalación y Ejecución</a></li>
  </ul>
</details>

---

<br>

## 🪆 El patrón Decorator

El patrón **Decorator** es un patrón estructural que permite añadir funcionalidades o responsabilidades a **instancias (objetos)** de una clase base, evitando cargarlas con lógicas opcionales que no forman parte de su naturaleza esencial.

Para lograrlo, el patrón propone la creación de una **Interface Común** que debe ser implementada tanto por esa clase base como por todas aquellas clases (llamadas **decoradores**) que sirvan para añadir esas funcionalidades o responsabilidades adicionales. Con la finalidad **de** reutilizar código y estandarizar las clases decoradores, éstas deberán extender de una **clase abstracta** que actúa de "modelo" para construir dichas clases.

En definitiva, el patrón Decorator permite que una aplicación construya procesos complejos y modulares "apilando" envoltorios sobre un objeto base, donde cada capa añade una capacidad única sin que el cliente (el componente que usa el objeto) necesite conocer la complejidad de la cadena de procesamiento.

<br>

### 🧩 Elementos típicos que encontramos en un patrón Decorator

Este patrón propone una arquitectura basada en los siguientes elementos clave:

1️⃣ **Interface Común (`InputFormatInterface`)**

Es el contrato que estandariza la forma de uso tanto del objeto inicial como de todos sus decoradores. Gracias a ella, la aplicación cliente depende de una **abstracción y no de concreciones**, desconociendo si está llamando al objeto base o a un decorador, lo que le permite funcionar sin necesidad de conocer la complejidad interna de la cadena.

2️⃣ **Componente Concreto (`AppInput`)**

Es la clase que representa el objeto original al que queremos añadir capacidades.

Dependiendo del contexto, este componente puede ser una clase con lógica compleja preexistente (como un sistema de envío de emails) o, como en nuestro ejemplo, una clase creada específicamente para **convertir un dato primitivo (un string) en un objeto**.

En este último caso, su función es vital para "objetivizar" el contenido, permitiendo que el patrón pueda empezar a trabajar sobre una instancia que rinda cuentas a la interfaz común.

3️⃣ **Clase Abstracta Decoradora (`AbstractDecorator`)**

Actúa como el modelo para todos los decoradores.

Su función es proporcionar una **propiedad específica destinada a almacenar la referencia** al objeto que se va a decorar (ya sea el núcleo original o un objeto que ya ha sido decorado previamente), facilitando así la delegación de llamadas hacia el interior de la cadena.

4️⃣ **Decoradores Concretos**

Son las clases (como `MarkdownDecorator`, `DangerousHTMLTagsDecorator` o `PlainTextDecorator`) que heredan del modelo abstracto para inyectar una lógica específica de filtrado, limpieza o transformación, antes o después de pasar la ejecución al siguiente elemento.

<br>

### 👍🏼 ¿Cuándo usar el patrón Decorator?

Su aplicación es ideal en situaciones donde la herencia tradicional se vuelve rígida o ineficiente.

Este patrón permite **modificar el comportamiento en tiempo de ejecución**. Mientras que la herencia es estática (se define al programar), el Decorator permite decidir qué funciones añadir y en qué orden mientras la aplicación ya está corriendo, adaptándose dinámicamente a las necesidades del momento.

Podemos identificar varios escenarios clave donde nos conviene usar este patrón.

#### 📌 Extensión de Funcionalidades Existentes

Cuando ya disponemos de una clase que realiza una tarea concreta (ej. un `Notificador` de emails) y necesitamos añadirle capacidades adicionales (ej. enviar también por SMS, Slack o Facebook) sin modificar la clase original. Es la solución perfecta para evitar la **explosión combinatoria de subclases**.

#### 📌 "Objetivización" de contenidos no decorables

Como hemos visto en nuestro proyecto, a veces queremos decorar algo que no es un objeto por naturaleza (como un `string` o un flujo de datos crudos). Usamos el patrón para crear una **clase base portadora** que "envuelve" ese dato, convirtiéndolo en un objeto que permite iniciar una cadena de procesamiento modular.

#### 📌 Clases "Final" o selladas

Cuando trabajas con librerías externas donde las clases están marcadas como `final` (no se pueden heredar). El Decorator es la única forma de extender su comportamiento envolviéndolas en un wrapper propio.

<br>

### 🎯 Principales beneficios de aplicar el patrón Decorator

El uso del Decorator no solo resuelve problemas de extensibilidad, sino que mejora la calidad del código siguiendo las mejores prácticas de la **Programación Orientada a Objetos (POO)**.

#### 📌 Cumplimiento de Principios SOLID

*   **Principio de Responsabilidad Única (SRP)**

Permite desglosar una clase monolítica que hace muchas cosas en varias clases pequeñas y especializadas. Cada decorador hace una sola cosa (ej. uno filtra HTML, otro convierte Markdown).

*   **Principio de Abierto/Cerrado (OCP)**:

Puedes introducir nuevos decoradores y funcionalidades sin tocar el código de las clases existentes ni el de los clientes que las usan. El sistema está "cerrado" a modificación pero "abierto" a extensión.

#### 📌 Composición vs. Herencia

Favorece la **composición sobre la herencia**. La herencia es una relación de "ser" (es estática), mientras que la composición/decoración es una relación de "tener" y "envolver" (es dinámica). Esto hace que el sistema sea mucho más flexible y menos propenso a errores de jerarquías complejas.

#### 📌 Modularidad y Reutilización

Los decoradores son piezas independientes que pueden combinarse de infinitas maneras. Un decorador de "Seguridad" configurado una vez puede reutilizarse para decorar un sistema de archivos, una base de datos o un simple campo de texto.

#### 📌 Transparencia para el Cliente

Gracias al uso de una interfaz común, el cliente no necesita saber si está tratando con el objeto básico o con un objeto envuelto en diez capas de decoración. Esto reduce el acoplamiento y facilita el mantenimiento.


<br>

[🔝](#top)

---

<br>

## 🧪 Ejemplo de implementación: Sistema de formateo de contenido para web

### 🔎 Explicación detallada del ejemplo

Dado el detalle con el que se explica el ejemplo creado para visualizar el patrón Decorator, se ha decidido crear un documento específico para ello.

👉🏼 [Explicación detallada del ejemplo](EJEMPLO_DE_IMPLEMENTACION.md)

<br>

### 🤔 Entendiendo el patrón Decorator

En este documento encontrarás una explicación más detallada de cómo encaja el patrón Decorator en el ejemplo creado:

👉🏼 [Entendiendo el patrón Decorator](ENTENDIENDO_DECORATOR.md)


### 👉🏼 Identificación de los principales archivos del ejemplo

#### 📁 Carpeta src

###### 📁 Carpeta MyApp: el núcleo de la aplicación

    - `AppInput.php`: componente concreto que sirve como base de la decoración (convierte el texto en objeto "decorable").
    - `InputFormatInterface.php`: interfaz común que garantiza que el cliente pueda tratar a todos por igual.

###### 📁 Carpeta Decorators: los decoradores

    - `AbstractDecorator.php`: clase abstracta que estandariza la estructura de todos los decoradores.
    - `MarkdownDecorator.php`: decorador concreto que transforma sintaxis Markdown en código HTML.
    - `DangerousHTMLTagsDecorator.php`: decorador concreto que elimina etiquetas y atributos HTML peligrosos.
    - `PlainTextDecorator.php`: decorador concreto que limpia cualquier rastro de HTML dejando solo texto plano.

#### 📁 Carpeta Client

 - `WebsiteClient.php`: aplicación cliente que depende de la abstracción para procesar el contenido.

#### ➡️ Flujo de ejecución

Ubicado en la raíz del proyecto: `main.php`.

#### 🎞️ Visualización de resultados

Interfaz visual para comparar los resultados.

Ubicado en la raíz del proyecto: `index.php` y `styles.css`.

<br>

[🔝](#top)

---

<br>

## 📂 Estructura del Proyecto y Composer

### 1. Organización del código en `src/`

Para mantener el orden hemos movido todo el código fuente a la carpeta `src/`.

### 2. Autocarga con Composer (PSR-4)

En lugar de tener una lista interminable de `require_once "archivo.php"` en nuestro `main.php`, utilizamos **Composer** para la carga automática de clases.

El archivo `composer.json` define el mapeo:
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

Esto significa que cualquier clase con el namespace que empiece por `App\` será buscada automáticamente dentro de la carpeta `src/`. Por ejemplo, la clase `AppInput` estará en el namespace `App\MyApp` y se buscará en `src/MyApp`.

Gracias a esto, en nuestro `main.php` solo necesitamos una línea para cargar TODO el proyecto:

```php
require "vendor/autoload.php";
```

<br>

[🔝](#top)

---

<br>

## 📋 Requisitos

- **PHP 8.0** o superior.
- **[Composer](https://getcomposer.org/)**: Necesario para generar el mapa de clases (autoload).

<br>

## 🚀 Instalación y Ejecución

### 1. Instalación

1.  Clona este repositorio o descarga los archivos.
2.  Abre una terminal en la carpeta raíz del proyecto.
3.  Ejecuta el siguiente comando para generar la carpeta `vendor` y el autoloader:

    ```bash
    composer dump-autoload
    ```
    > 💡 **Nota**: Como este proyecto no tiene dependencias de librerías externas (solo usamos Composer para el autoload), basta con `composer dump-autoload`. Si hubiera librerías en `require`, usaríamos `composer install`.

### 2. Ejecución

Puedes ejecutar/visualizar la aplicación mediante el **navegador** (con XAMPP o con un servidor web local).

#### 🌐 Para ejecutarlo mediante XAMPP:

1. Mueve la carpeta del proyecto a la carpeta htdocs (o equivalente según la versión de XAMPP y sistema operativo que uses).
2. Arranca XAMPP.
3. Accede a index.php desde tu navegador (por ejemplo: http://localhost/patrones/decorator/index.php)

#### 🌐 Para ejecutarlo usando el servidor web interno de PHP

PHP trae un servidor web ligero que sirve para desarrollo. No necesitas instalar Apache ni XAMPP.

1. Abre la terminal y navega a la carpeta de tu proyecto:

```bash
cd ~/Documentos/.../patrones/decorator
```
2. Dentro de esa ubicación, ejecuta:

```bash
php -S localhost:8000
```

>💡 No es obligatorio usar el puerto 8000, puedes usar el que desees, por ejemplo, el 8001.

Con esto, lo que estás haciendo es crear un servidor web php (cuya carpeta raíz es la carpeta seleccionada), que está escuchando en el puerto 8000 (o en el que hayas elegido).

>💡 Si quisieras, podrías crear simultáneamente tantos servidores como proyectos tengas en tu ordenador, siempre y cuando cada uno estuviera escuchando en un puerto diferente (8001, 8002, ...).

3. Ahora, abre tu navegador y accede a http://localhost:8000

Ya podrás visualizar el documento index.php con toda la información del ejemplo.

>💡 No es necesario indicar `http://localhost:8000/index.php` porque el servidor va a buscar dentro de la carpeta raíz (en este caso, en Documentos/.../patrones/decorator), un archivo index.php o index.html de forma automática. Si existe, lo sirve como página principal.
>
> Por eso, estas dos URLs funcionan igual:
>
> http://localhost:8000
>
> http://localhost:8000/index.php


<br>

[🔝](#top)