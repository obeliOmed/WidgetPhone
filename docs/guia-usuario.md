# WidgetPhone — Guía de usuario

## ¿Qué hace este campo?

El campo de teléfono valida el número, lo guarda en un formato internacional estándar, y lo muestra de forma legible cuando ya está guardado.

## Cómo usarlo

1. Escribe el teléfono como te resulte más natural: `600123456`, `600 123 456`, `+34 600 123 456`, `0034600123456`... todos funcionan igual.
2. Al salir del campo aparece un aviso:
   - **✓** (verde) — número con formato plausible.
   - **✗** (rojo) — no parece un teléfono válido.
3. Al guardar, el sistema convierte el número a formato internacional estándar (ej. `+34600123456`).
4. Al volver a abrir el registro, el número se muestra ya formateado y legible: `+34 600 12 34 56`.

## País por defecto

El campo asume España (`ES`) salvo que el administrador haya configurado otro país por defecto para ese formulario en concreto (por ejemplo, formularios pensados para clientes de Francia o Reino Unido).

## Preguntas frecuentes

**¿Por qué me borra los espacios/guiones que escribí?**
No los borra — los normaliza. El teléfono se guarda internamente en formato internacional compacto (`+34600123456`) pero se muestra siempre bien espaciado al leer.

**¿Puedo escribir un número de otro país?**
Sí, escribiendo el prefijo internacional (`+44...` para Reino Unido, `+33...` para Francia, etc.) el sistema lo reconoce igualmente, aunque el campo esté configurado por defecto para España.

**¿Qué pasa si el número no es válido?**
Se guarda tal cual lo escribiste (sin normalizar) para que no se pierda la información, pero el aviso rojo (✗) te indica que revises el dato.

**¿Puedo dejarlo en blanco?**
Sí, el campo vacío no da error.
