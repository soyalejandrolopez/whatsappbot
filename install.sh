#!/bin/bash

# ChatBot WhatsApp - Script de Instalación Automatizada
# Este script automatiza la instalación completa del sistema

set -e

echo "🚀 Iniciando instalación de ChatBot WhatsApp..."
echo "=================================================="

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar mensajes
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar requisitos del sistema
check_requirements() {
    print_status "Verificando requisitos del sistema..."
    
    # Verificar PHP
    if ! command -v php &> /dev/null; then
        print_error "PHP no está instalado. Por favor instala PHP 8.1 o superior."
        exit 1
    fi
    
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    print_success "PHP $PHP_VERSION encontrado"
    
    # Verificar Composer
    if ! command -v composer &> /dev/null; then
        print_error "Composer no está instalado. Por favor instala Composer."
        exit 1
    fi
    
    print_success "Composer encontrado"
    
    # Verificar Node.js
    if ! command -v node &> /dev/null; then
        print_warning "Node.js no está instalado. Se necesita para compilar assets."
    else
        NODE_VERSION=$(node --version)
        print_success "Node.js $NODE_VERSION encontrado"
    fi
    
    # Verificar MySQL
    if ! command -v mysql &> /dev/null; then
        print_warning "MySQL no está instalado o no está en el PATH."
    else
        print_success "MySQL encontrado"
    fi
}

# Instalar dependencias de PHP
install_php_dependencies() {
    print_status "Instalando dependencias de PHP..."
    composer install --no-dev --optimize-autoloader
    print_success "Dependencias de PHP instaladas"
}

# Instalar dependencias de Node.js
install_node_dependencies() {
    if command -v npm &> /dev/null; then
        print_status "Instalando dependencias de Node.js..."
        npm install
        print_success "Dependencias de Node.js instaladas"
        
        print_status "Compilando assets..."
        npm run build
        print_success "Assets compilados"
    else
        print_warning "npm no está disponible. Saltando instalación de dependencias de Node.js"
    fi
}

# Configurar archivo de entorno
setup_environment() {
    print_status "Configurando archivo de entorno..."
    
    if [ ! -f .env ]; then
        cp .env.example .env
        print_success "Archivo .env creado desde .env.example"
    else
        print_warning "El archivo .env ya existe. No se sobrescribirá."
    fi
    
    # Generar clave de aplicación
    php artisan key:generate --force
    print_success "Clave de aplicación generada"
}

# Configurar base de datos
setup_database() {
    print_status "Configurando base de datos..."
    
    # Solicitar credenciales de base de datos
    echo ""
    echo "Por favor, proporciona las credenciales de tu base de datos MySQL:"
    read -p "Host de la base de datos [127.0.0.1]: " DB_HOST
    DB_HOST=${DB_HOST:-127.0.0.1}
    
    read -p "Puerto de la base de datos [3306]: " DB_PORT
    DB_PORT=${DB_PORT:-3306}
    
    read -p "Nombre de la base de datos [chatbot_whatsapp]: " DB_DATABASE
    DB_DATABASE=${DB_DATABASE:-chatbot_whatsapp}
    
    read -p "Usuario de la base de datos [root]: " DB_USERNAME
    DB_USERNAME=${DB_USERNAME:-root}
    
    read -s -p "Contraseña de la base de datos: " DB_PASSWORD
    echo ""
    
    # Actualizar archivo .env
    sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
    sed -i "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
    
    print_success "Credenciales de base de datos configuradas"
    
    # Ejecutar migraciones
    print_status "Ejecutando migraciones de base de datos..."
    php artisan migrate --force
    print_success "Migraciones ejecutadas"
    
    # Ejecutar seeders
    print_status "Poblando base de datos con datos iniciales..."
    php artisan db:seed --force
    print_success "Datos iniciales insertados"
}

# Configurar WhatsApp
setup_whatsapp() {
    print_status "Configurando WhatsApp Business API..."
    
    echo ""
    echo "Para configurar WhatsApp Business API, necesitas:"
    echo "1. Token de acceso de WhatsApp Business API"
    echo "2. ID del número de teléfono"
    echo "3. ID de la cuenta de negocio"
    echo "4. Token de verificación del webhook"
    echo ""
    
    read -p "¿Deseas configurar WhatsApp ahora? (y/n) [n]: " SETUP_WHATSAPP
    SETUP_WHATSAPP=${SETUP_WHATSAPP:-n}
    
    if [[ $SETUP_WHATSAPP =~ ^[Yy]$ ]]; then
        read -p "Token de acceso de WhatsApp: " WHATSAPP_ACCESS_TOKEN
        read -p "ID del número de teléfono: " WHATSAPP_PHONE_NUMBER_ID
        read -p "ID de la cuenta de negocio: " WHATSAPP_BUSINESS_ACCOUNT_ID
        read -p "Token de verificación del webhook: " WHATSAPP_WEBHOOK_VERIFY_TOKEN
        
        # Actualizar archivo .env
        sed -i "s/WHATSAPP_ACCESS_TOKEN=.*/WHATSAPP_ACCESS_TOKEN=$WHATSAPP_ACCESS_TOKEN/" .env
        sed -i "s/WHATSAPP_PHONE_NUMBER_ID=.*/WHATSAPP_PHONE_NUMBER_ID=$WHATSAPP_PHONE_NUMBER_ID/" .env
        sed -i "s/WHATSAPP_BUSINESS_ACCOUNT_ID=.*/WHATSAPP_BUSINESS_ACCOUNT_ID=$WHATSAPP_BUSINESS_ACCOUNT_ID/" .env
        sed -i "s/WHATSAPP_WEBHOOK_VERIFY_TOKEN=.*/WHATSAPP_WEBHOOK_VERIFY_TOKEN=$WHATSAPP_WEBHOOK_VERIFY_TOKEN/" .env
        
        print_success "Configuración de WhatsApp completada"
    else
        print_warning "Configuración de WhatsApp omitida. Puedes configurarla más tarde editando el archivo .env"
    fi
}

# Inicializar flujos del chatbot
initialize_chatbot() {
    print_status "Inicializando flujos del chatbot..."
    php artisan chatbot:init-flows --force
    print_success "Flujos del chatbot inicializados"
}

# Configurar permisos
setup_permissions() {
    print_status "Configurando permisos de archivos..."
    
    # Crear directorios si no existen
    mkdir -p storage/logs
    mkdir -p storage/framework/cache
    mkdir -p storage/framework/sessions
    mkdir -p storage/framework/views
    mkdir -p bootstrap/cache
    
    # Configurar permisos
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    
    print_success "Permisos configurados"
}

# Optimizar para producción
optimize_production() {
    read -p "¿Es esta una instalación de producción? (y/n) [n]: " IS_PRODUCTION
    IS_PRODUCTION=${IS_PRODUCTION:-n}
    
    if [[ $IS_PRODUCTION =~ ^[Yy]$ ]]; then
        print_status "Optimizando para producción..."
        
        # Cambiar entorno a producción
        sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
        sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
        
        # Optimizar configuración
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        
        print_success "Optimización de producción completada"
        print_warning "Recuerda configurar tu servidor web para apuntar al directorio 'public/'"
    fi
}

# Mostrar información final
show_final_info() {
    echo ""
    echo "🎉 ¡Instalación completada exitosamente!"
    echo "========================================"
    echo ""
    echo "📋 Información importante:"
    echo "• URL de la aplicación: $(grep APP_URL .env | cut -d '=' -f2)"
    echo "• Panel administrativo: $(grep APP_URL .env | cut -d '=' -f2)/admin"
    echo "• Usuario administrador: admin@chatbot.com"
    echo "• Contraseña: admin123"
    echo ""
    echo "🔧 Próximos pasos:"
    echo "1. Configura tu servidor web para apuntar al directorio 'public/'"
    echo "2. Configura el webhook de WhatsApp en: $(grep APP_URL .env | cut -d '=' -f2)/api/whatsapp/webhook"
    echo "3. Cambia las credenciales del administrador por defecto"
    echo "4. Personaliza los flujos del chatbot según tus necesidades"
    echo ""
    echo "📚 Documentación:"
    echo "• Manual de usuario: docs/MANUAL_USUARIO.md"
    echo "• Ejemplos de flujos: docs/EJEMPLOS_FLUJOS.md"
    echo "• README: README.md"
    echo ""
    print_success "¡El ChatBot WhatsApp está listo para usar!"
}

# Función principal
main() {
    check_requirements
    install_php_dependencies
    install_node_dependencies
    setup_environment
    setup_database
    setup_whatsapp
    initialize_chatbot
    setup_permissions
    optimize_production
    show_final_info
}

# Ejecutar instalación
main "$@"
