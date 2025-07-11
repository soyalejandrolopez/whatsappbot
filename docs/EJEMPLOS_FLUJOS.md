# Ejemplos de Flujos de Conversación

## 📋 Índice

1. [Flujo de Bienvenida](#flujo-de-bienvenida)
2. [Flujo de Soporte Técnico](#flujo-de-soporte-técnico)
3. [Flujo de Ventas](#flujo-de-ventas)
4. [Flujo de Información de Productos](#flujo-de-información-de-productos)
5. [Flujo de Horarios y Contacto](#flujo-de-horarios-y-contacto)
6. [Flujo de Satisfacción](#flujo-de-satisfacción)
7. [Flujos Avanzados](#flujos-avanzados)

## 🚀 Flujo de Bienvenida

### Descripción
Flujo principal que se ejecuta cuando un cliente inicia una conversación por primera vez.

### Configuración
- **Tipo de Activación**: Bienvenida
- **Prioridad**: 100 (máxima)
- **Idioma**: Español

### Estructura del Flujo

```json
{
  "name": "Flujo de Bienvenida",
  "trigger_type": "welcome",
  "trigger_conditions": {
    "is_new_conversation": true
  },
  "flow_steps": [
    {
      "step": 1,
      "type": "message",
      "content": "¡Hola! 👋 Bienvenido a *TechSolutions*\n\nSoy tu asistente virtual y estoy aquí para ayudarte. ¿En qué puedo asistirte hoy?",
      "next_step": 2
    },
    {
      "step": 2,
      "type": "interactive_menu",
      "content": "Selecciona una opción:",
      "options": [
        {"id": "productos", "title": "📦 Ver productos"},
        {"id": "soporte", "title": "🔧 Soporte técnico"},
        {"id": "ventas", "title": "💰 Información de ventas"},
        {"id": "horarios", "title": "🕒 Horarios de atención"},
        {"id": "agente", "title": "👤 Hablar con agente"}
      ],
      "next_step": 3
    },
    {
      "step": 3,
      "type": "process_selection",
      "conditions": [
        {"input": "productos", "next_flow": "products_flow"},
        {"input": "soporte", "next_flow": "support_flow"},
        {"input": "ventas", "next_flow": "sales_flow"},
        {"input": "horarios", "next_flow": "hours_flow"},
        {"input": "agente", "action": "transfer_to_agent"}
      ],
      "default_action": "not_understood"
    }
  ]
}
```

### Ejemplo de Conversación
```
Bot: ¡Hola! 👋 Bienvenido a *TechSolutions*

Soy tu asistente virtual y estoy aquí para ayudarte. ¿En qué puedo asistirte hoy?

[Botones interactivos]
📦 Ver productos
🔧 Soporte técnico  
💰 Información de ventas
🕒 Horarios de atención
👤 Hablar con agente

Cliente: [Selecciona "Ver productos"]

Bot: [Ejecuta flujo de productos]
```

## 🔧 Flujo de Soporte Técnico

### Descripción
Flujo especializado para resolver problemas técnicos comunes y escalar cuando es necesario.

### Configuración
- **Tipo de Activación**: Palabra clave + Opción de menú
- **Palabras clave**: ["problema", "error", "ayuda", "soporte", "técnico", "falla"]
- **Prioridad**: 80

### Estructura del Flujo

```json
{
  "name": "Soporte Técnico",
  "trigger_type": "keyword",
  "trigger_conditions": {
    "keywords": ["problema", "error", "ayuda", "soporte", "técnico", "falla"],
    "menu_option": "soporte"
  },
  "flow_steps": [
    {
      "step": 1,
      "type": "message",
      "content": "🔧 *Soporte Técnico*\n\nEstoy aquí para ayudarte con cualquier problema técnico. Para brindarte la mejor asistencia, necesito conocer más detalles.",
      "next_step": 2
    },
    {
      "step": 2,
      "type": "interactive_menu",
      "content": "¿Qué tipo de problema tienes?",
      "options": [
        {"id": "login", "title": "🔐 No puedo acceder"},
        {"id": "lento", "title": "🐌 Sistema muy lento"},
        {"id": "error", "title": "⚠️ Mensaje de error"},
        {"id": "funcionalidad", "title": "⚙️ Función no trabaja"},
        {"id": "otro", "title": "❓ Otro problema"}
      ],
      "next_step": 3
    },
    {
      "step": 3,
      "type": "process_selection",
      "conditions": [
        {
          "input": "login",
          "response": "🔐 *Problemas de Acceso*\n\nVamos a resolver esto paso a paso:\n\n1️⃣ Verifica que tu usuario y contraseña sean correctos\n2️⃣ Intenta restablecer tu contraseña desde el enlace 'Olvidé mi contraseña'\n3️⃣ Limpia la caché y cookies de tu navegador\n4️⃣ Intenta desde otro navegador o dispositivo\n\n¿Alguno de estos pasos resolvió tu problema?",
          "next_step": 4
        },
        {
          "input": "lento",
          "response": "🐌 *Sistema Lento*\n\nPara mejorar el rendimiento:\n\n1️⃣ Cierra otras aplicaciones y pestañas del navegador\n2️⃣ Verifica tu conexión a internet\n3️⃣ Actualiza tu navegador a la última versión\n4️⃣ Reinicia tu dispositivo\n\n¿Esto mejoró la velocidad?",
          "next_step": 4
        },
        {
          "input": "error",
          "response": "⚠️ *Mensaje de Error*\n\nPara ayudarte mejor, necesito más información:\n\n📝 Por favor comparte:\n• El mensaje de error exacto\n• En qué pantalla aparece\n• Qué estabas haciendo cuando ocurrió\n• Si es la primera vez que sucede\n\nUn técnico especializado revisará tu caso.",
          "action": "transfer_to_agent"
        },
        {
          "input": "funcionalidad",
          "response": "⚙️ *Función No Funciona*\n\nVamos a diagnosticar el problema:\n\n1️⃣ ¿Qué función específica no está trabajando?\n2️⃣ ¿Aparece algún mensaje cuando intentas usarla?\n3️⃣ ¿Funcionaba antes correctamente?\n4️⃣ ¿Has notado el problema en otras funciones?\n\nPor favor proporciona estos detalles para ayudarte mejor.",
          "action": "collect_details"
        },
        {
          "input": "otro",
          "response": "❓ *Otro Problema*\n\nPor favor describe tu problema con el mayor detalle posible:\n\n• ¿Qué está sucediendo exactamente?\n• ¿Cuándo comenzó el problema?\n• ¿Has intentado alguna solución?\n\nNuestro equipo técnico te ayudará personalmente.",
          "action": "transfer_to_agent"
        }
      ],
      "default_action": "not_understood"
    },
    {
      "step": 4,
      "type": "interactive_menu",
      "content": "¿Se resolvió tu problema?",
      "options": [
        {"id": "si", "title": "✅ Sí, está resuelto"},
        {"id": "no", "title": "❌ No, necesito más ayuda"},
        {"id": "parcial", "title": "🔄 Parcialmente"}
      ],
      "next_step": 5
    },
    {
      "step": 5,
      "type": "process_selection",
      "conditions": [
        {
          "input": "si",
          "response": "¡Excelente! 🎉 Me alegra haber podido ayudarte.\n\n¿Hay algo más en lo que pueda asistirte?",
          "action": "satisfaction_survey"
        },
        {
          "input": "no",
          "response": "Entiendo. Te voy a conectar con un especialista técnico que podrá ayudarte de manera personalizada.",
          "action": "transfer_to_agent"
        },
        {
          "input": "parcial",
          "response": "Te conectaré con un técnico para completar la solución de tu problema.",
          "action": "transfer_to_agent"
        }
      ]
    }
  ]
}
```

## 💰 Flujo de Ventas

### Descripción
Flujo para capturar leads, proporcionar información de productos y generar cotizaciones.

### Configuración
- **Tipo de Activación**: Palabra clave + Opción de menú
- **Palabras clave**: ["precio", "costo", "comprar", "cotización", "venta"]
- **Prioridad**: 70

### Estructura del Flujo

```json
{
  "name": "Información de Ventas",
  "trigger_type": "keyword",
  "trigger_conditions": {
    "keywords": ["precio", "costo", "comprar", "cotización", "venta"],
    "menu_option": "ventas"
  },
  "flow_steps": [
    {
      "step": 1,
      "type": "message",
      "content": "💰 *Información de Ventas*\n\n¡Perfecto! Estás interesado en nuestros productos y servicios. Me da mucho gusto ayudarte a encontrar la solución ideal para tu negocio.",
      "next_step": 2
    },
    {
      "step": 2,
      "type": "interactive_menu",
      "content": "¿Qué tipo de información necesitas?",
      "options": [
        {"id": "precios", "title": "💵 Ver precios"},
        {"id": "demo", "title": "🎯 Solicitar demo"},
        {"id": "cotizacion", "title": "📋 Pedir cotización"},
        {"id": "comparar", "title": "⚖️ Comparar planes"},
        {"id": "agente_ventas", "title": "👤 Hablar con ventas"}
      ],
      "next_step": 3
    },
    {
      "step": 3,
      "type": "process_selection",
      "conditions": [
        {
          "input": "precios",
          "response": "💵 *Nuestros Precios*\n\n🔹 **Plan Básico**: $299/mes\n   • Hasta 100 usuarios\n   • Funciones esenciales\n   • Soporte por email\n\n🔹 **Plan Profesional**: $599/mes\n   • Hasta 500 usuarios\n   • Funciones avanzadas\n   • Soporte prioritario\n\n🔹 **Plan Enterprise**: Precio personalizado\n   • Usuarios ilimitados\n   • Funciones premium\n   • Soporte 24/7\n\n¿Te interesa algún plan en particular?",
          "next_step": 4
        },
        {
          "input": "demo",
          "response": "🎯 *Solicitud de Demostración*\n\n¡Excelente elección! Una demostración te permitirá ver todas las funcionalidades en acción.\n\nPara programar tu demo personalizada, necesito algunos datos:\n\n📝 **Información requerida:**\n• Nombre de tu empresa\n• Tu nombre y cargo\n• Número de empleados\n• Fecha y hora preferida\n• Funcionalidades de mayor interés\n\nUn especialista en ventas se pondrá en contacto contigo.",
          "action": "transfer_to_sales_agent"
        },
        {
          "input": "cotizacion",
          "response": "📋 *Solicitud de Cotización*\n\nPerfecto, prepararemos una cotización personalizada para tu empresa.\n\n📋 **Para una cotización precisa necesitamos:**\n• Nombre de la empresa\n• Giro o industria\n• Número de usuarios estimado\n• Funcionalidades específicas requeridas\n• Presupuesto aproximado\n• Tiempo estimado de implementación\n\nUn ejecutivo de ventas te contactará en las próximas 2 horas.",
          "action": "transfer_to_sales_agent"
        },
        {
          "input": "comparar",
          "response": "⚖️ *Comparación de Planes*\n\n📊 **Tabla Comparativa:**\n\n| Característica | Básico | Profesional | Enterprise |\n|---|---|---|---|\n| Usuarios | 100 | 500 | Ilimitado |\n| Almacenamiento | 10GB | 100GB | Ilimitado |\n| Integraciones | 5 | 25 | Ilimitadas |\n| Soporte | Email | Prioritario | 24/7 |\n| Reportes | Básicos | Avanzados | Personalizados |\n| Precio/mes | $299 | $599 | Cotización |\n\n¿Qué plan se adapta mejor a tus necesidades?",
          "next_step": 4
        },
        {
          "input": "agente_ventas",
          "response": "👤 *Conexión con Ventas*\n\nTe estoy conectando con uno de nuestros especialistas en ventas. Ellos podrán resolver todas tus dudas y ayudarte a elegir la mejor solución.\n\n⏱️ Tiempo de espera estimado: 2-3 minutos",
          "action": "transfer_to_sales_agent"
        }
      ]
    },
    {
      "step": 4,
      "type": "interactive_menu",
      "content": "¿Qué te gustaría hacer ahora?",
      "options": [
        {"id": "mas_info", "title": "ℹ️ Más información"},
        {"id": "solicitar_demo", "title": "🎯 Solicitar demo"},
        {"id": "hablar_ventas", "title": "👤 Hablar con ventas"},
        {"id": "menu_principal", "title": "🏠 Menú principal"}
      ],
      "next_step": 5
    }
  ]
}
```

## 📦 Flujo de Información de Productos

### Descripción
Flujo detallado para mostrar información específica de productos y servicios.

### Ejemplo de Conversación Completa

```
Cliente: Hola, quiero información sobre sus productos

Bot: 📦 *Nuestros Productos y Servicios*

Tenemos soluciones completas para empresas de todos los tamaños. ¿Qué tipo de producto te interesa?

[Lista interactiva]
🖥️ Software de Gestión
   • CRM - Gestión de clientes
   • ERP - Planificación de recursos

🛠️ Servicios Profesionales  
   • Consultoría especializada
   • Desarrollo a medida

Cliente: [Selecciona "CRM - Gestión de clientes"]

Bot: 🔧 *CRM - Sistema de Gestión de Clientes*

✅ **Características principales:**
• Gestión completa de contactos y leads
• Seguimiento automático de ventas
• Automatización de marketing por email
• Reportes y analíticas avanzadas
• Integración con WhatsApp, email y redes sociales
• App móvil incluida

💰 **Precio:** Desde $299/mes
👥 **Usuarios:** Hasta 100 usuarios incluidos
📊 **Almacenamiento:** 50GB incluidos

¿Te gustaría una demostración gratuita?

[Botones]
🎯 Solicitar demo gratuita
💰 Ver planes y precios
📞 Hablar con especialista
🏠 Menú principal

Cliente: [Selecciona "Solicitar demo gratuita"]

Bot: 🎯 *Solicitud de Demo Gratuita*

¡Excelente! Una demostración te permitirá ver el CRM en acción con datos reales.

📅 **Duración:** 30-45 minutos
💻 **Modalidad:** Online (Zoom/Teams)
🎁 **Incluye:** Consultoría gratuita de 1 hora

Para programar tu demo, un especialista se pondrá en contacto contigo en las próximas 2 horas.

¿Hay algún día y horario de tu preferencia?

Cliente: Mañana en la tarde estaría bien

Bot: Perfecto, he registrado tu preferencia para mañana en la tarde.

📝 **Resumen de tu solicitud:**
• Producto: CRM - Gestión de Clientes  
• Demo: 30-45 minutos
• Preferencia: Mañana en la tarde
• Incluye: Consultoría gratuita

Un especialista te contactará pronto para confirmar el horario exacto.

¿Hay algo más en lo que pueda ayudarte?

[Botones]
📦 Ver otros productos
💰 Información de precios
🏠 Menú principal
```

## 🕒 Flujo de Horarios y Contacto

### Estructura Simplificada

```json
{
  "name": "Horarios y Contacto",
  "trigger_type": "keyword",
  "trigger_conditions": {
    "keywords": ["horario", "contacto", "teléfono", "dirección", "ubicación"],
    "menu_option": "horarios"
  },
  "flow_steps": [
    {
      "step": 1,
      "type": "message",
      "content": "🕒 *Horarios de Atención*\n\n📅 **Lunes a Viernes:** 9:00 AM - 6:00 PM\n📅 **Sábados:** 9:00 AM - 2:00 PM\n📅 **Domingos:** Cerrado\n\n🌎 **Zona horaria:** México (GMT-6)\n\n📞 **Contacto:**\n• Teléfono: +52 55 1234 5678\n• Email: contacto@techsolutions.com\n• WhatsApp: Este mismo chat 😊\n\n📍 **Oficinas:**\nAv. Reforma 123, Col. Centro\nCiudad de México, CDMX 06000",
      "next_step": 2
    },
    {
      "step": 2,
      "type": "interactive_menu",
      "content": "¿Necesitas algo más?",
      "options": [
        {"id": "agendar_cita", "title": "📅 Agendar cita"},
        {"id": "emergencia", "title": "🚨 Soporte urgente"},
        {"id": "ubicacion", "title": "📍 Cómo llegar"},
        {"id": "menu", "title": "🏠 Menú principal"}
      ]
    }
  ]
}
```

## ⭐ Flujo de Satisfacción

### Descripción
Flujo para recopilar feedback y calificaciones de los clientes.

### Ejemplo de Implementación

```
Bot: ⭐ *Califica tu Experiencia*

Para mejorar nuestro servicio, ¿podrías calificar tu experiencia de hoy?

[Botones de calificación]
⭐⭐⭐⭐⭐ Excelente (5)
⭐⭐⭐⭐ Muy bueno (4)  
⭐⭐⭐ Bueno (3)
⭐⭐ Regular (2)
⭐ Malo (1)

Cliente: [Selecciona 5 estrellas]

Bot: ¡Excelente! 🎉 

Nos encanta saber que tuviste una experiencia perfecta. Tu opinión es muy valiosa para nosotros.

¿Te gustaría agregar algún comentario adicional sobre nuestro servicio?

Cliente: Todo muy bien, el bot es muy útil y rápido

Bot: ¡Muchas gracias por tu comentario! 😊

Hemos registrado tu calificación de 5 estrellas y tu comentario positivo. Esto nos ayuda mucho a seguir mejorando.

¿Hay algo más en lo que pueda ayudarte hoy?

[Botones]
🏠 Menú principal
👋 Terminar conversación
```

## 🚀 Flujos Avanzados

### Flujo de Escalación Inteligente

```json
{
  "name": "Escalación Inteligente",
  "description": "Detecta cuándo transferir automáticamente a un agente",
  "trigger_conditions": {
    "failed_responses": 3,
    "keywords_urgency": ["urgente", "emergencia", "crítico", "inmediato"],
    "sentiment": "negative",
    "conversation_length": 10
  },
  "actions": [
    {
      "condition": "failed_responses >= 3",
      "message": "Veo que no he podido resolver tu consulta adecuadamente. Te voy a conectar con uno de nuestros especialistas.",
      "action": "transfer_to_agent"
    },
    {
      "condition": "urgency_detected",
      "message": "Entiendo que es un asunto urgente. Te estoy conectando inmediatamente con nuestro equipo de soporte prioritario.",
      "action": "transfer_to_priority_agent"
    }
  ]
}
```

### Flujo de Seguimiento Post-Venta

```json
{
  "name": "Seguimiento Post-Venta",
  "trigger_type": "scheduled",
  "trigger_conditions": {
    "days_after_purchase": 7
  },
  "flow_steps": [
    {
      "step": 1,
      "type": "message",
      "content": "¡Hola {nombre}! 👋\n\nHa pasado una semana desde que adquiriste nuestro {producto}. ¿Cómo ha sido tu experiencia hasta ahora?\n\n¿Necesitas ayuda con la configuración o tienes alguna pregunta?"
    }
  ]
}
```

---

## 💡 Consejos para Crear Flujos Efectivos

### Mejores Prácticas

1. **Mantén los mensajes concisos**: Máximo 2-3 líneas por mensaje
2. **Usa emojis apropiados**: Hacen la conversación más amigable
3. **Ofrece opciones claras**: Máximo 5 botones por mensaje
4. **Incluye escape routes**: Siempre permite volver al menú principal
5. **Personaliza con variables**: Usa el nombre del cliente cuando sea posible
6. **Planifica la escalación**: Define cuándo transferir a humanos
7. **Prueba regularmente**: Verifica que todos los flujos funcionen correctamente

### Errores Comunes a Evitar

❌ **Flujos muy largos**: Más de 10 pasos sin escalación
❌ **Opciones confusas**: Botones con texto ambiguo  
❌ **Sin salidas**: Flujos sin opción de volver atrás
❌ **Información desactualizada**: Precios o datos obsoletos
❌ **Falta de personalización**: Mensajes muy genéricos
❌ **No considerar errores**: Qué pasa si el usuario escribe algo inesperado

¡Con estos ejemplos puedes crear flujos de conversación efectivos que mejoren la experiencia de tus clientes! 🚀
