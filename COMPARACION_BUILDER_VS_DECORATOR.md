# 🏗️ vs 🪆 Comparativa: Builder vs Decorator

Decidí abordar esta comparación porque, en una primera aproximación, ambos patrones parecían ser muy similares, puesto que en los dos se produce una cierta acumulación de lógica o características.

Sin embargo, sus propósitos y estructuras son fundamentalmente diferentes, por lo que he creido conveniente mantener esta guía para identificar sus diferencias y entender cuándo utilizar cada uno de ellos.

👉🏼 [Volver al README](README.md)

## 🎯 Objetivo: qué busca cada patrón

*   **Builder (Patrón Creacional)**: su objetivo es **CONSTRUIR** un objeto complejo. Se centra en el **proceso de creación** paso a paso, ocultando la complejidad de cómo se ensamblan las piezas. Al final, se obtiene una instancia lista para usar.

*   **Decorator (Patrón Estructural)**: su objetivo es **EXTENDER** la funcionalidad de un objeto que ya existe. Se centra en el **comportamiento dinámico**. No crea un objeto nuevo desde cero, sino que "viste" o añade capacidades a uno ya existente.

## 🧩 Estructura: cómo se organiza cada patrón

*   **Builder**: es un proceso **lineal y atómico**. Se invocan métodos secuencialmente (`paso1()`, `paso2()`, etc.) y finalmente se obtiene un **único objeto final** (el producto). Si el proceso se interrumpe, el objeto suele estar incompleto.

*   **Decorator**: es una estructura **recursiva (capas de cebolla)**. Cada decorador envuelve al anterior. No es un solo objeto con muchas propiedades, sino una **pila de objetos independientes** donde cada uno delega la ejecución al que tiene debajo.

## 🎓 Analogías didácticas

- #### 🍕 Builder: construir una pizza
    Necesitas la masa, luego el tomate, luego el queso y finalmente el horneado. Un "Director" conoce la receta. Al final el cliente recibe **una pizza**. El enfoque es el montaje y el orden de los ingredientes.

- #### 🥸 Decorator: disfrazar a una persona
    Tienes a una persona (objeto base). Le pones una capa (decorador 1). Encima le pones una máscara (decorador 2). Luego una espada (decorador 3). La persona sigue siendo funcional (puede caminar o hablar), pero ahora tiene "poderes" o aspectos adicionales. Los disfraces se pueden añadir o quitar en tiempo de ejecución sin reconstruir a la persona.

## ⚠️ Diferencias técnicas clave

| Característica | Builder | Decorator |
| :--- | :--- | :--- |
| **Categoría** | Creacional (Crear) | Estructural (Organizar) |
| **Punto de Enfoque** | El proceso de ensamblaje | La extensión de responsabilidades |
| **Resultado** | Un solo objeto complejo | Una cadena de objetos envueltos |
| **Temporalidad** | Antes de que el objeto exista | Cuando el objeto ya está en uso |
| **Interficie** | El constructor tiene sus propios métodos | Todos (base y decoradores) comparten la misma interfaz |

## 🤔 ¿Cuándo elegir cada uno?

*   Usa el **Builder** cuando tengas un objeto que requiere una configuración compleja y larga, con muchas partes opcionales pero que, una vez montadas, forman una unidad estática.

*   Usa el **Decorator** cuando necesites añadir o quitar funcionalidades a un objeto de forma dinámica, permitiendo combinaciones infinitas de comportamientos que el objeto base no puede (ni debe) prever.

👉🏼 [Volver al README](README.md)