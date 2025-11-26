/**
 * Payment Options Styling - ePayco Module
 * Organiza el logo y título de los medios de pago
 * Logo (21px x 21px) + Título
 */

document.addEventListener('DOMContentLoaded', function() {
    // Esperar a que PrestaShop renderice los payment options
    setTimeout(function() {
        const paymentOptions = document.querySelectorAll('.payment-option');
        
        paymentOptions.forEach(function(option) {
            // Obtener logo e imagen
            const logo = option.querySelector('img.payment-option-logo');
            const title = option.querySelector('.payment-option-title');
            
            if (logo && title) {
                // Limpiar el contenido
                option.innerHTML = '';
                
                // Re-agregar en el orden correcto: logo primero, luego título
                option.appendChild(logo);
                option.appendChild(title);
            }
        });
    }, 100);
});
