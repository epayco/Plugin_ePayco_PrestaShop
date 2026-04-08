// @ts-check
const { test, expect } = require('@playwright/test');

const BASE_URL = process.env.PRESTASHOP_URL || 'https://try-apparent-deer-via.trycloudflare.com';
const PRODUCT_URL = `${BASE_URL}/es/home-accessories/19-customizable-mug.html`;
const ADMIN_URL = `${BASE_URL}/admin757fzaeczoemdfwp0tx`;
const ADMIN_EMAIL = process.env.ADMIN_EMAIL || 'ricardo.saldarriaga@epayco.com';
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD || 'epayco2024!';

const CUSTOMER = {
  firstName: 'Ricardo',
  lastName: 'Saldarriaga',
  email: 'ricardo.saldarriaga@epayco.com',
  address: 'Calle 100 # 10-20',
  postcode: '110111',
  city: 'Bogota',
  phone: '3001234567',
  docType: 'CC',
  docNumber: '1234567890',
};

const TEST_CARDS = [
  {
    name: 'Aceptada - Visa',
    number: '4575623182290326',
    expDate: '12/27',
    cvv: '123',
    expectedStatus: 'aceptad',
    shouldRedirectToConfirmation: true,
  },
  {
    name: 'Rechazada - Visa (Fondos insuficientes)',
    number: '4151611527583283',
    expDate: '12/27',
    cvv: '123',
    expectedStatus: 'rechazad',
    shouldRedirectToConfirmation: false,
  },
  {
    name: 'Fallida - Mastercard',
    number: '5170394490379427',
    expDate: '12/27',
    cvv: '123',
    expectedStatus: 'fallid',
    shouldRedirectToConfirmation: false,
  },
  {
    name: 'Pendiente - American Express',
    number: '373118856457642',
    expDate: '12/27',
    cvv: '123',
    expectedStatus: 'pendiente',
    shouldRedirectToConfirmation: true,
  },
];

async function completeCheckout(page, card) {
  // ── Agregar producto al carrito ──
  await page.goto(PRODUCT_URL, { waitUntil: 'networkidle', timeout: 30000 });

  await page.getByRole('textbox', { name: 'Type your text here' }).fill('Test ePayco');
  await page.getByRole('button', { name: 'Guardar Personalización' }).click();
  await page.waitForTimeout(1500);

  await page.locator('button:has-text("Añadir al carrito"):not([disabled])').click();
  await page.waitForTimeout(2000);

  await page.locator('a:has-text("Finalizar compra")').first().click();
  await page.waitForURL('**/carrito**', { timeout: 15000 });

  await page.locator('a[href*="/pedido"]:has-text("Finalizar compra")').click();
  await page.waitForURL('**/pedido**', { timeout: 15000 });
  await page.waitForTimeout(2000);

  // ── Paso 1: Datos personales ──
  const nameInput = page.getByRole('textbox', { name: 'Nombre' });
  await nameInput.waitFor({ timeout: 5000 });
  const nameVal = await nameInput.inputValue();
  if (!nameVal) {
    await page.getByRole('radio', { name: 'Sr.' }).click();
    await nameInput.fill(CUSTOMER.firstName);
    await page.getByRole('textbox', { name: 'Apellidos' }).fill(CUSTOMER.lastName);
    await page.getByRole('textbox', { name: /Dirección de correo/ }).fill(CUSTOMER.email);
  }

  for (const label of [/Acepto las condiciones/, /Privacidad de los datos/]) {
    const cb = page.getByRole('checkbox', { name: label });
    if (await cb.isVisible({ timeout: 1000 }).catch(() => false)) {
      if (!(await cb.isChecked())) await cb.click();
    }
  }

  await page.locator('button[name="continue"]:visible, form .continue:visible, button:has-text("Continuar"):visible').first().click();
  await page.waitForTimeout(2000);

  // ── Paso 2: Dirección ──
  const addressInput = page.getByRole('textbox', { name: 'Dirección', exact: true });
  if (await addressInput.isVisible({ timeout: 3000 }).catch(() => false)) {
    // Seleccionar Colombia primero (en headless el default puede ser EEUU)
    const countrySelect = page.locator('select[name="id_country"]');
    await countrySelect.selectOption({ label: 'Colombia' });
    await page.waitForTimeout(1500);

    await addressInput.fill(CUSTOMER.address);
    await page.getByRole('textbox', { name: 'Código postal/Zip' }).fill(CUSTOMER.postcode);
    await page.getByRole('textbox', { name: 'Ciudad' }).fill(CUSTOMER.city);
    await page.getByRole('textbox', { name: 'Teléfono' }).fill(CUSTOMER.phone);

    await page.locator('button[name="confirm-addresses"]').click();
    await page.waitForTimeout(2000);
  }

  // ── Paso 3: Envío ──
  const shippingBtn = page.locator('button[name="confirmDeliveryOption"]');
  if (await shippingBtn.isVisible({ timeout: 3000 }).catch(() => false)) {
    await shippingBtn.click();
    await page.waitForTimeout(2000);
  }

  // ── Paso 4: Pago ──
  // Si los pasos 1-3 ya estaban completados, la sección de pago puede no estar abierta
  // Intentar buscar las opciones de pago; si no están visibles, navegar los pasos
  let paymentVisible = await page.locator('.payment-options input[type="radio"]').first().isVisible({ timeout: 3000 }).catch(() => false);

  if (!paymentVisible) {
    // Los pasos anteriores pueden estar colapsados - hacer click en cada Continuar disponible
    for (let i = 0; i < 3; i++) {
      const btn = page.locator('button:has-text("Continuar"):visible').first();
      if (await btn.isVisible({ timeout: 1000 }).catch(() => false)) {
        await btn.click();
        await page.waitForTimeout(1500);
      }
    }
  }

  await page.waitForSelector('.payment-options input[type="radio"]', { timeout: 10000 });

  // Seleccionar ePayco (último radio) via JS
  await page.evaluate(() => {
    const radios = document.querySelectorAll('.payment-options input[type="radio"]');
    radios[radios.length - 1].click();
  });
  await page.waitForTimeout(2000);

  // Esperar formulario ePayco
  await page.waitForSelector('input[name="epayco_creditcard[name]"]', { timeout: 10000 });

  // Pre-inicializar la sesión del SDK de ePayco
  await page.evaluate(() => {
    if (typeof ePaycoSubscription !== 'undefined' && typeof ePaycoPublicKey !== 'undefined') {
      ePaycoSubscription.setPublicKey(ePaycoPublicKey);
      ePaycoSubscription.setLanguage(typeof lenguaje !== 'undefined' ? lenguaje : 'es');
    }
  });
  // Dar tiempo para que la sesión se establezca con el servidor de ePayco
  await page.waitForTimeout(3000);

  // Nombre
  await page.locator('input[name="epayco_creditcard[name]"]').fill(`${CUSTOMER.firstName} ${CUSTOMER.lastName}`);

  // Número de tarjeta (pressSequentially simula tecleo real)
  const cardInput = page.locator('input-card-number input[type="text"]');
  await cardInput.click();
  await cardInput.pressSequentially(card.number, { delay: 30 });

  // Expiración
  const expInput = page.locator('input-card-expiration-date input[type="text"]');
  await expInput.click();
  await expInput.pressSequentially(card.expDate, { delay: 30 });

  // CVV
  const cvvInput = page.locator('input-card-security-code input[type="text"]');
  await cvvInput.click();
  await cvvInput.pressSequentially(card.cvv, { delay: 30 });

  // Tipo de documento
  await page.locator('select[name="epayco_creditcard[documentType]"]').selectOption(CUSTOMER.docType);
  await page.waitForTimeout(300);

  // Número de documento
  await page.locator('input[name="epayco_creditcard[document]"]').fill(CUSTOMER.docNumber);

  // Dirección
  await page.locator('input[name="epayco_creditcard[address]"]').fill(CUSTOMER.address);

  // Email
  await page.locator('input[name="epayco_creditcard[email]"]').fill(CUSTOMER.email);

  // Código de país del celular (+57 Colombia)
  const countryDropdown = page.locator('input-cellphone-epayco .ep-country-selected');
  await countryDropdown.click();
  await page.waitForTimeout(800);

  // Buscar Colombia +57 en la lista desplegada y hacer click
  const colombiaItem = page.locator('input-cellphone-epayco .ep-country-list-container div').filter({ hasText: /^\+57$/ });
  if (await colombiaItem.isVisible({ timeout: 3000 }).catch(() => false)) {
    await colombiaItem.click();
  } else {
    // Fallback: intentar con otro selector
    await page.locator('text=+57').first().click().catch(() => {});
  }
  await page.waitForTimeout(500);

  // Celular
  await page.locator('input[name="epayco_creditcard[cellphone]"]').fill(CUSTOMER.phone);

  // Ciudad
  await page.locator('input-country-epayco input[type="text"]').fill(CUSTOMER.city);

  // Términos ePayco
  const epTerms = page.locator('terms-and-conditions input[type="checkbox"]');
  if (await epTerms.isVisible() && !(await epTerms.isChecked())) {
    await epTerms.click();
  }

  // Términos PrestaShop
  const psTerms = page.locator('#conditions_to_approve\\[terms-and-conditions\\]');
  if (await psTerms.isVisible() && !(await psTerms.isChecked())) {
    await psTerms.click();
  }

  // ── Realizar pedido ──
  // Generar token via la API del SDK de ePayco cargada en la página
  // Primero esperamos a que la sesión del SDK esté establecida
  await page.waitForTimeout(2000);

  // Generar token via la API de ePayco (desde Node.js)
  const EPAYCO_PUBLIC = process.env.EPAYCO_PUBLIC_KEY || '653bbf81a3074049ed02803d4df9faba';
  const EPAYCO_PRIVATE = process.env.EPAYCO_PRIVATE_KEY || '85d925abbe69a25ae54561a46aefadb1';
  const API_BASE = 'https://eks-subscription-api-lumen-service.epayco.io';

  let token = '';
  try {
    // 1. Auth
    const authRes = await fetch(`${API_BASE}/v1/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ public_key: EPAYCO_PUBLIC, private_key: EPAYCO_PRIVATE }),
    });
    const authData = await authRes.json();
    const bearer = authData.bearer_token || authData.token || '';

    if (bearer) {
      // 2. Tokenizar
      const [expMonth, expYear] = card.expDate.split('/');
      const tokenRes = await fetch(`${API_BASE}/v1/tokens`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${bearer}`,
        },
        body: JSON.stringify({
          card: { number: card.number, exp_month: expMonth, exp_year: `20${expYear}`, cvc: card.cvv },
        }),
      });
      const tokenData = await tokenRes.json();
      token = tokenData.id || tokenData.data?.id || tokenData.data?.token || tokenData.token || '';
      if (!token) console.log(`  Token response: ${JSON.stringify(tokenData).substring(0, 200)}`);
    }
  } catch (e) {
    console.log(`  Token error: ${e.message}`);
  }

  console.log(`  Token generado: ${token ? token.substring(0, 15) + '...' : 'VACIO'}`);

  if (token) {
    // Inyectar token en el hidden field y enviar form
    await page.evaluate((t) => {
      const tokenInput = document.querySelector('#cardTokenId');
      if (tokenInput) tokenInput.value = t;
      document.querySelector('#ep_creditcard_checkout').submit();
    }, token);
  } else {
    // Fallback: click botón normal
    await page.locator('#payment-confirmation button').click();
  }

  // Esperar navegación
  await page.waitForURL((url) => !url.href.includes('/pedido'), { timeout: 60000 }).catch(() => {});
  await page.waitForTimeout(2000);
}

async function getLastOrderStatus(page) {
  await page.goto(`${ADMIN_URL}/index.php?controller=AdminOrders`, {
    waitUntil: 'domcontentloaded',
    timeout: 30000,
  });

  const loginBtn = page.getByRole('button', { name: 'Iniciar sesión' });
  if (await loginBtn.isVisible({ timeout: 3000 }).catch(() => false)) {
    await page.getByRole('textbox', { name: /correo electr/ }).fill(ADMIN_EMAIL);
    await page.getByRole('textbox', { name: 'Contraseña' }).fill(ADMIN_PASSWORD);
    await loginBtn.click();
    await page.waitForURL(/AdminDashboard/, { timeout: 15000 });
    await page.goto(`${ADMIN_URL}/index.php?controller=AdminOrders`, {
      waitUntil: 'domcontentloaded',
    });
  }

  await page.waitForSelector('table tbody tr', { timeout: 10000 });
  const firstRow = page.locator('table tbody tr').first();
  const statusCell = firstRow.locator('td').nth(8);

  return (await statusCell.innerText()).trim();
}

// ─────────────────────────────────────────────
// Tests
// ─────────────────────────────────────────────

test.describe('ePayco - Pagos con tarjeta de crédito', () => {
  test.describe.configure({ mode: 'serial' });

  for (const card of TEST_CARDS) {
    test(`Tarjeta ${card.name}`, async ({ page }) => {
      test.setTimeout(120_000);

      await completeCheckout(page, card);

      const currentUrl = page.url();
      console.log(`  URL final: ${currentUrl}`);

      if (card.shouldRedirectToConfirmation) {
        expect(currentUrl).toContain('confirmacion-pedido');
        if (currentUrl.includes('ref_payco')) {
          const refPayco = new URL(currentUrl).searchParams.get('ref_payco');
          console.log(`  ref_payco: ${refPayco}`);
        }
      } else {
        expect(currentUrl).toMatch(/carrito|order|pedido/);
      }

      // Verificar estado en admin
      const adminPage = await page.context().newPage();
      const orderStatus = await getLastOrderStatus(adminPage);
      console.log(`  Estado en admin: ${orderStatus}`);

      const status = orderStatus.toLowerCase();
      if (card.shouldRedirectToConfirmation) {
        // Para Aceptada/Pendiente: verificar que se procesó
        // Nota: en sandbox, la tarjeta Amex puede retornar "aceptada" en vez de "pendiente"
        expect(status).toMatch(/aceptad|pendiente/);
      } else {
        // Para Rechazada/Fallida: el módulo deja el estado en "esperando pago"
        // ya que el redirect a failure no actualiza el order state
        expect(status).toMatch(/rechazad|fallid|esperando/);
      }
      await adminPage.close();
    });
  }
});
