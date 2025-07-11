# Manual de Usuario - ChatBot WhatsApp

## 📖 Índice

1. [Introducción](#introducción)
2. [Acceso al Sistema](#acceso-al-sistema)
3. [Panel de Control](#panel-de-control)
4. [Gestión de Conversaciones](#gestión-de-conversaciones)
5. [Configuración del Chatbot](#configuración-del-chatbot)
6. [Analíticas y Reportes](#analíticas-y-reportes)
7. [Administración de Usuarios](#administración-de-usuarios)
8. [Preguntas Frecuentes](#preguntas-frecuentes)

## 🚀 Introducción

El ChatBot WhatsApp es un sistema integral de atención al cliente que automatiza las conversaciones a través de WhatsApp Business API. Este manual te guiará a través de todas las funcionalidades del sistema.

### Beneficios Principales
- **Atención 24/7**: El chatbot responde automáticamente a los clientes
- **Escalación Inteligente**: Transferencia automática a agentes humanos cuando es necesario
- **Analíticas Detalladas**: Métricas completas de rendimiento y satisfacción
- **Flujos Personalizables**: Conversaciones adaptadas a tu negocio

## 🔐 Acceso al Sistema

### Iniciar Sesión
1. Ve a la URL de tu sistema: `https://tu-dominio.com/admin`
2. Ingresa tu email y contraseña
3. Haz clic en "Iniciar Sesión"

### Credenciales por Defecto
- **Email**: admin@chatbot.com
- **Contraseña**: admin123
- **Rol**: Administrador

> ⚠️ **Importante**: Cambia estas credenciales inmediatamente después del primer acceso.

### Recuperar Contraseña
1. En la pantalla de login, haz clic en "¿Olvidaste tu contraseña?"
2. Ingresa tu email
3. Revisa tu correo para el enlace de recuperación
4. Sigue las instrucciones para crear una nueva contraseña

## 🏠 Panel de Control

### Dashboard Principal
El dashboard te muestra una vista general del sistema con:

#### Métricas Principales
- **Total de Conversaciones**: Número total de chats iniciados
- **Conversaciones Activas**: Chats en curso actualmente
- **Total de Contactos**: Base de datos de clientes
- **Mensajes Hoy**: Actividad del día actual

#### Gráficos y Estadísticas
- **Actividad Semanal**: Tendencias de conversaciones y mensajes
- **Tipos de Conversación**: Distribución entre bot y agentes humanos
- **Conversaciones Recientes**: Lista de los últimos chats

#### Top Agentes
- Ranking de agentes por conversaciones resueltas
- Métricas de rendimiento individual

### Navegación
El menú lateral incluye:
- 📊 **Dashboard**: Vista general
- 💬 **Conversaciones**: Gestión de chats
- 📞 **Contactos**: Base de datos de clientes
- 🤖 **Flujos del Bot**: Configuración de conversaciones
- 💭 **Respuestas del Bot**: Templates de mensajes
- 👥 **Usuarios**: Gestión de agentes y administradores
- 📈 **Analíticas**: Reportes detallados

## 💬 Gestión de Conversaciones

### Ver Conversaciones
1. Ve a **Conversaciones** en el menú lateral
2. Verás una lista con todas las conversaciones
3. Usa los filtros para encontrar conversaciones específicas:
   - **Estado**: Activa, Cerrada, En espera
   - **Tipo**: Chatbot, Humano, Mixto
   - **Agente**: Conversaciones asignadas a agentes específicos

### Información de Conversaciones
Cada conversación muestra:
- **Contacto**: Nombre y número de teléfono
- **Estado**: Estado actual de la conversación
- **Agente Asignado**: Si está siendo atendida por un humano
- **Último Mensaje**: Timestamp del último intercambio
- **Acciones**: Botones para ver, asignar o cerrar

### Ver Detalles de Conversación
1. Haz clic en el botón "Ver" (👁️) de cualquier conversación
2. Verás el historial completo de mensajes
3. Información del contacto en el panel lateral
4. Opciones para:
   - Enviar mensajes manuales
   - Asignar a un agente
   - Cerrar la conversación
   - Ver analíticas específicas

### Enviar Mensajes Manuales
1. En la vista de conversación, ve al campo de texto inferior
2. Escribe tu mensaje
3. Haz clic en "Enviar"
4. El mensaje se enviará inmediatamente por WhatsApp

### Asignar Conversaciones
1. En la lista de conversaciones, haz clic en "Asignar"
2. Selecciona un agente disponible
3. La conversación cambiará a modo "Humano"
4. El agente recibirá una notificación

### Cerrar Conversaciones
1. Haz clic en "Cerrar" en la conversación deseada
2. Confirma la acción
3. La conversación se marcará como "Cerrada"
4. Se registrará en las estadísticas

## 🤖 Configuración del Chatbot

### Flujos de Conversación

#### Ver Flujos Existentes
1. Ve a **Flujos del Bot** en el menú
2. Verás todos los flujos configurados
3. Información mostrada:
   - **Nombre**: Identificador del flujo
   - **Tipo de Activación**: Cómo se inicia el flujo
   - **Estado**: Activo/Inactivo
   - **Uso**: Número de veces utilizado
   - **Última Vez Usado**: Timestamp del último uso

#### Crear Nuevo Flujo
1. Haz clic en "Crear Nuevo Flujo"
2. Completa la información básica:
   - **Nombre**: Nombre descriptivo
   - **Descripción**: Explicación del propósito
   - **Idioma**: Selecciona el idioma (español por defecto)
   - **Prioridad**: Orden de evaluación (mayor número = mayor prioridad)

3. Configura las **Condiciones de Activación**:
   - **Bienvenida**: Se activa al iniciar conversación
   - **Palabra Clave**: Se activa con palabras específicas
   - **Opción de Menú**: Se activa con selecciones de menú

4. Define los **Pasos del Flujo**:
   - **Mensaje**: Envía un mensaje al usuario
   - **Esperar Entrada**: Pausa para recibir respuesta del usuario
   - **Procesar Entrada**: Evalúa la respuesta y decide el siguiente paso
   - **Acción Especial**: Ejecuta acciones como transferir a agente

#### Editar Flujos Existentes
1. Haz clic en "Editar" en el flujo deseado
2. Modifica los campos necesarios
3. Guarda los cambios
4. El flujo se actualizará inmediatamente

#### Activar/Desactivar Flujos
1. Usa el botón de toggle en la lista de flujos
2. Los flujos inactivos no se ejecutarán
3. Útil para pruebas o mantenimiento temporal

### Respuestas del Bot

#### Gestionar Respuestas Predefinidas
1. Ve a **Respuestas del Bot**
2. Verás todas las respuestas configuradas organizadas por categoría:
   - **Saludo**: Mensajes de bienvenida
   - **Menú**: Opciones de navegación
   - **Error**: Mensajes de error y no entendido
   - **Despedida**: Mensajes de cierre
   - **Información**: Datos de la empresa

#### Editar Respuestas
1. Haz clic en "Editar" en la respuesta deseada
2. Modifica el texto del mensaje
3. Configura datos adicionales si es necesario:
   - **Botones**: Para mensajes interactivos
   - **Listas**: Para opciones múltiples
   - **Variables**: Campos dinámicos como {nombre}

4. Guarda los cambios

#### Crear Nueva Respuesta
1. Haz clic en "Nueva Respuesta"
2. Completa:
   - **Clave**: Identificador único
   - **Categoría**: Tipo de respuesta
   - **Mensaje**: Texto a enviar
   - **Idioma**: Español por defecto
   - **Variables**: Campos dinámicos opcionales

### Variables Dinámicas
Puedes usar variables en tus mensajes:
- `{nombre}`: Nombre del contacto
- `{telefono}`: Número de teléfono
- `{fecha}`: Fecha actual
- `{hora}`: Hora actual
- `{empresa}`: Nombre de tu empresa

Ejemplo: "Hola {nombre}, gracias por contactar a {empresa}"

## 📈 Analíticas y Reportes

### Dashboard de Analíticas
1. Ve a **Analíticas** en el menú principal
2. Verás métricas detalladas:

#### Métricas de Satisfacción
- Distribución de calificaciones (1-5 estrellas)
- Promedio de satisfacción
- Comentarios de clientes

#### Tiempo de Respuesta
- Tiempo promedio de respuesta de agentes
- Tendencias por día/semana/mes
- Comparación entre agentes

#### Tasa de Resolución
- Porcentaje de conversaciones resueltas
- Tiempo promedio de resolución
- Conversaciones escaladas vs resueltas por bot

#### Actividad por Horas
- Picos de actividad durante el día
- Planificación de turnos de agentes
- Optimización de recursos

### Reportes Automáticos

#### Reporte Diario
Se genera automáticamente cada día con:
- Resumen de actividad
- Nuevos contactos
- Conversaciones iniciadas y cerradas
- Métricas de satisfacción
- Tiempo promedio de respuesta

#### Exportar Datos
1. En la sección de analíticas, haz clic en "Exportar"
2. Selecciona el rango de fechas
3. Elige el formato (CSV, Excel)
4. Descarga el archivo generado

### Métricas por Agente
- Conversaciones asignadas
- Conversaciones resueltas
- Tiempo promedio de respuesta
- Calificación promedio de satisfacción
- Mensajes enviados

## 👥 Administración de Usuarios

### Gestión de Usuarios
1. Ve a **Usuarios** en el menú
2. Verás todos los usuarios del sistema

#### Roles Disponibles
- **Administrador**: Acceso completo al sistema
- **Agente**: Puede gestionar conversaciones asignadas
- **Usuario**: Acceso limitado solo a sus conversaciones

#### Crear Nuevo Usuario
1. Haz clic en "Nuevo Usuario"
2. Completa la información:
   - **Nombre**: Nombre completo
   - **Email**: Dirección de correo electrónico
   - **Contraseña**: Contraseña segura
   - **Teléfono**: Número de contacto
   - **Rol**: Selecciona el rol apropiado
   - **Estado**: Activo/Inactivo

3. Guarda el usuario

#### Editar Usuarios Existentes
1. Haz clic en "Editar" en el usuario deseado
2. Modifica los campos necesarios
3. Guarda los cambios

#### Desactivar Usuarios
1. Cambia el estado a "Inactivo"
2. El usuario no podrá acceder al sistema
3. Sus conversaciones asignadas se liberarán

### Permisos y Accesos
- **Administradores**: Pueden gestionar todo el sistema
- **Agentes**: Solo ven conversaciones asignadas y pueden enviar mensajes
- **Usuarios**: Acceso de solo lectura a sus propias conversaciones

## ❓ Preguntas Frecuentes

### ¿Cómo configuro mi número de WhatsApp?
1. Necesitas una cuenta de WhatsApp Business API
2. Configura las credenciales en el archivo `.env`
3. Verifica el webhook con WhatsApp
4. Prueba enviando un mensaje de prueba

### ¿Puedo personalizar las respuestas del bot?
Sí, completamente. Ve a **Respuestas del Bot** y edita cualquier mensaje. También puedes crear respuestas completamente nuevas.

### ¿Cómo transfiero una conversación a un agente?
1. Ve a la conversación específica
2. Haz clic en "Asignar a Agente"
3. Selecciona el agente disponible
4. La conversación cambiará automáticamente a modo humano

### ¿Puedo ver estadísticas de rendimiento?
Sí, la sección **Analíticas** tiene reportes detallados de:
- Satisfacción del cliente
- Tiempos de respuesta
- Volumen de conversaciones
- Rendimiento por agente

### ¿Qué hago si el bot no responde?
1. Verifica que los flujos estén activos
2. Revisa las respuestas predefinidas
3. Confirma la configuración de WhatsApp API
4. Revisa los logs del sistema

### ¿Puedo hacer backup de mis datos?
Sí, recomendamos:
1. Backup diario de la base de datos
2. Exportar analíticas regularmente
3. Guardar configuraciones de flujos importantes

### ¿Cómo agrego más agentes?
1. Ve a **Usuarios**
2. Crea un nuevo usuario con rol "Agente"
3. Proporciona las credenciales al nuevo agente
4. Asigna conversaciones según sea necesario

---

## 📞 Soporte Técnico

Si necesitas ayuda adicional:
- **Email**: soporte@tuempresa.com
- **Teléfono**: +52 55 1234 5678
- **Horario**: Lunes a Viernes, 9:00 AM - 6:00 PM

¡Gracias por usar nuestro ChatBot WhatsApp! 🚀
