# 🪆 Ejemplo de implementación del patrón Decorator

## 🌐 Sistema de formateo de contenido web

Imagina que eres el desarrollador responsable de un sitio web tipo **foro o comunidad online** (similar a Reddit, Stack Overflow, o un blog con comentarios). Tu sitio tiene diferentes secciones donde los usuarios pueden publicar contenido:

1. **Contenido de la propia web creado por el administrador**
2. **Contenido creado por editores de confianza**
3. **Mensajes privados entre usuarios premium**
4. **Posts en el foro de la comunidad**
5. **Comentarios en artículos del blog**

Dado que cada usuario tiene libertad para escribir el contenido a publicar en las diferentes secciones, existe el riesgo de que algún usuario malintencionado intente publicar contenido que pueda dañar el sitio web o robar datos de los usuarios (scripts maliciosos, etiquetas HTML no permitidas, etc).

Por otro lado, como puede ser que algunos de los usuarios que publiquen contenido no conozcan el lenguaje HTML pero sí el formato Markdown, que es más sencillo de escribir, se hace necesario "traducir" este tipo de sintaxis a HTML.

Por todos estos motivos, necesitamos implementar en nuestra web un sistema que nos permita aplicar diferentes filtros y/o conversiones al contenido publicado por los usuarios.

👉🏼 [Volver al README](README.md)

---
---

## 👩💻 ¿Qué tipo de contenido puede escribir un usuario?

Cuando un usuario escribe en un formulario (independientemente del destino de este), tiene la libertad de escribir diferentes tipos de contenido:

### ▪️ **Texto plano**

```
Hola, me ha gustado mucho este artículo.
Gracias por compartirlo.
```

- Sin ningún formato
- Sin caracteres especiales de HTML o Markdown
- Completamente seguro

### ▪️ **Markdown**

```markdown
# Título
Esto es **negrita** y esto es *cursiva*.
```

- Sintaxis de marcado ligera
- Fácil de escribir para usuarios no técnicos
- Necesita convertirse a HTML para mostrarse con formato
- Relativamente seguro (no puede ejecutar scripts directamente)

### ▪️ **HTML**

```html
<h1>Título</h1>
<p>Esto es un <strong>párrafo</strong> con formato.</p>
```

- Código HTML directo
- Da control completo sobre el formato
- **PELIGROSO**: puede contener scripts maliciosos
- Solo apropiado para usuarios de confianza

### ▪️ **HTML con scripts maliciosos**

```html
Hola <script>
  document.location = 'http://sitio-phishing.com';
</script>
```

- HTML que incluye código JavaScript
- **MUY PELIGROSO**: puede robar datos, redirigir usuarios, etc.
- Debe ser filtrado SIEMPRE en contenido público

### ▪️ **Markdown + HTML mezclado**

```markdown
# Mi Post
Esto es **importante** y aquí hay HTML: <b>negrita</b>
<script>alert('ataque')</script>
```

- Usuario escribe Markdown pero también añade HTML (intencionalmente o no)
- Necesita procesarse en orden: primero convertir Markdown, luego filtrar HTML
- Común cuando usuarios copian/pegan de otras fuentes

### ▪️ **HTML con atributos peligrosos (ataques XSS reales)**

```html
<a href="#" onclick="robarDatos()">Click aquí</a>
<img src="x" onerror="ejecutarMalware()">
```

---
---

## 🛡️-🧑‍🦰 El problema principal: seguridad vs experiencia de usuario

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

## 🎭 Escenarios posibles

Teniendo en cuenta todo lo dicho anteriormente, podemos imaginar una serie de posibles escenarios en los que nos puede interesar o no aplicar determinadas medidas correctivas o de seguridad.

### ◾ **Escenario A: administrador edita contenido del sitio**

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

### ◾ **Escenario B: editor de contenido confiable**

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

### ◾ **Escenario C: mensajes privados entre usuarios premium**

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

### ◾ **Escenario D: posts en foro de la comunidad**

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

### ◾ **Escenario E: comentarios públicos (anónimos)**

**Contexto:**

- Cualquier persona puede comentar sin registrarse.
- Riesgo de Spam y de inyectar código malicioso es máximo.

**Qué busca conseguir:**

1. **Intervención MÁXIMA:** Limpieza radical.
2. **Seguridad Total:** Eliminar absolutamente cualquier rastro de HTML.

**Solución técnica:**

- Usar `PlainTextDecorator` envolviendo al `AppInput`.
- Resultado: Texto plano puro. Si el usuario escribió `<b>Hola</b>`, el sistema mostrará simplemente `Hola`.

<br>

### ◾ Tabla resumen: Los 5 escenarios planteados y qué decoradores usan

| Escenario | Grado Intervención | Decoradores Usados | Qué se Consigue | Seguridad |
|-----------|--------------------|--------------------|-----------------|-----------|
| **A: Administrador** | **NULA** | *(ninguno, solo `AppInput`)* | Texto original intacto | ❌ Administrador |
| **B: Editor Confiable** | **BAJA** | `DangerousHTMLTagsDecorator` | HTML seguro, sin scripts | ✅ Alta |
| **C: Mensajes Premium** | **MEDIA** | `MarkdownDecorator` | Formato Markdown a HTML | ⚠️ Informativa |
| **D: Posts de Foro** | **ALTA** | `MarkdownDecorator` + `DangerousHTMLTagsDecorator` | Formato + Saneo selectivo | ✅ Alta |
| **E: Comentarios Anón.** | **MÁXIMA** | `PlainTextDecorator` | Texto plano puro, sin HTML | ✅ Máxima |

**Resumen de la arquitectura:**

- **`AppInput`**: El componente base (obligatorio).
- **`PlainTextDecorator`**: Se usa en Escenario E (seguridad radical).
- **`MarkdownDecorator`**: Se usa en Escenarios C y D (formateo).
- **`DangerousHTMLTagsDecorator`**: Se usa en Escenarios B y D (seguridad selectiva).

---
---

## 🤔 **¿Qué necesitamos implementar?**

Necesitamos un sistema que:

1. **Procese el texto** que envían los usuarios antes de guardarlo en la base de datos o mostrarlo
2. **Aplique diferentes reglas** según dónde se publique ese contenido
3. **Sea fácil de mantener** (si mañana decides permitir BBCode, que sea simple añadirlo)
4. **Sea flexible** (poder combinar reglas en diferentes órdenes)

👉🏼 [Volver al README](README.md)