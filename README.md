# ChatBot WhatsApp - Sistema Integral de Atención al Cliente

Un sistema completo de chatbot para WhatsApp desarrollado en Laravel con MySQL, diseñado para automatizar la atención al cliente y mejorar la experiencia de usuario.

## 🚀 Características Principales

- **Chatbot Inteligente**: Motor de conversación con IA integrada (OpenAI)
- **Integración WhatsApp**: Conexión completa con WhatsApp Business API
- **Panel Administrativo**: Dashboard completo para gestión y analíticas
- **Flujos Personalizables**: Sistema de flujos de conversación configurables
- **Analíticas Avanzadas**: Reportes detallados y métricas en tiempo real
- **Multiidioma**: Soporte completo en español con posibilidad de expansión
- **Seguridad Robusta**: Rate limiting, validaciones y logging de seguridad
- **Escalabilidad**: Diseñado para manejar múltiples clientes y alto volumen

## 📋 Requisitos del Sistema

### Requisitos Mínimos
- PHP 8.1 o superior
- MySQL 8.0 o superior
- Composer 2.0+
- Node.js 16+ (para assets)
- Redis (recomendado para cache y sesiones)

### Extensiones PHP Requeridas
- BCMath, Ctype, Fileinfo, JSON, Mbstring
- OpenSSL, PDO, Tokenizer, XML, cURL, GD

## 🛠️ Instalación

### 1. Instalar Dependencias
```bash
composer install
npm install
```

### 2. Configuración del Entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configurar Base de Datos
Edita `.env` con tus credenciales de MySQL:
```env
DB_DATABASE=chatbot_whatsapp
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 4. Configurar WhatsApp API
```env
WHATSAPP_ACCESS_TOKEN=tu_token_de_acceso
WHATSAPP_PHONE_NUMBER_ID=tu_phone_number_id
WHATSAPP_WEBHOOK_VERIFY_TOKEN=tu_token_de_verificacion
```

### 5. Ejecutar Migraciones
```bash
php artisan migrate
php artisan db:seed
```

## 📱 Uso Básico

### Acceso al Panel Administrativo
- URL: `https://tu-dominio.com/admin`
- Usuario por defecto: `admin@chatbot.com`
- Contraseña: `admin123`

### Secciones Principales
- **Dashboard**: Métricas y vista general
- **Conversaciones**: Gestión de chats activos
- **Contactos**: Base de datos de clientes
- **Flujos del Bot**: Configuración de conversaciones
- **Analíticas**: Reportes detallados

## 🔧 Comandos Útiles

```bash
# Generar reporte diario
php artisan chatbot:daily-report

# Limpiar cache
php artisan cache:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
```

## 🔒 Seguridad

- Rate limiting automático para WhatsApp
- Validación de webhooks
- Logging de eventos de seguridad
- Sanitización de entrada de datos

## 📊 Características Avanzadas

- **IA Integrada**: Respuestas inteligentes con OpenAI
- **Flujos Dinámicos**: Conversaciones personalizables
- **Analíticas en Tiempo Real**: Métricas detalladas
- **Transferencia a Agentes**: Escalación automática
- **Soporte Multiidioma**: Español nativo

## 🆘 Solución de Problemas

### Permisos
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Webhook
1. Verifica URL pública accesible
2. Confirma token de verificación
3. Revisa logs en `storage/logs/`

## 📞 Soporte

- Email: soporte@tuempresa.com
- Documentación completa en el proyecto
- Issues: GitHub Issues

## 📄 Licencia

MIT License - Ver archivo `LICENSE` para detalles.

---

Desarrollado con ❤️ para mejorar la atención al cliente a través de WhatsApp.
