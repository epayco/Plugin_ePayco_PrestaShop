# Changelog

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-es/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- **23 de octubre de 2025** - Actualización de campos para versión 2 del checkout
  - Actualizado el archivo `response.tpl` para mejorar la configuración del componente DetailPurchase
  - Corregida la concatenación de URL completa en el campo `returnUrl` usando JavaScript
  - Mejorada la configuración del idioma del componente con el campo `lang`
  - Actualizada la estructura de datos para compatibilidad con la nueva versión del checkout de ePayco

### Technical Details

- Modificado `payco/views/templates/front/response.tpl`:
  - Agregada coma faltante en la configuración del objeto
  - Implementada concatenación dinámica de URL completa para `returnUrl`
  - Utilización de `window.location.origin` y `window.location.pathname` para construir URLs absolutas

## [Previous Versions]

### [1.1.0] - Versiones anteriores

#### Funcionalidades Base

- Funcionalidades base del plugin ePayco para PrestaShop
- Integración con API de ePayco para procesamiento de pagos
- Gestión automática de órdenes y estados de pago
- Sistema de validación de firmas para seguridad de transacciones

#### Características Principales

- Soporte para múltiples métodos de pago (tarjetas de crédito, débito, PSE, etc.)
- Manejo automático de stock y restauración en caso de fallos
- Sistema de cron jobs para sincronización de estados
- Configuración de URLs de confirmación y respuesta personalizables
- Soporte para modo de pruebas y producción
- Validación de llaves públicas y privadas
- Manejo de múltiples monedas (COP, USD)

#### Implementación Técnica

- Clase principal Payco extendiendo PaymentModule de PrestaShop
- Sistema de logs integrado para debugging (`logs/cron.log`)
- Manejo de excepciones y errores robusto
- Validación de firmas SHA256 para seguridad
- API REST endpoints para confirmación y validación de pagos
- Sistema de metadata para tracking de transacciones
- Clases auxiliares: `EpaycoOrder`, `CreditCard_Order`, `CreditCard_OrderState`

#### Características de Seguridad

- Validación de firmas digitales
- Manejo seguro de credenciales
- Sanitización de datos de entrada
- Protección contra acceso directo a archivos (archivos `index.php`)
- Validación de IPs y datos de transacción
- Gestión segura de estados de órdenes y stock
