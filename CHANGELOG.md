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

- Funcionalidades base del plugin ePayco para PrestaShop
- Integración con API de ePayco
- Gestión de órdenes y estados de pago
- Manejo de stock y restauración automática
