// script.js – versión estable 28-abr-2025

// ───────────────────────── VARIABLES GLOBALES ─────────────────────────
let items = [];                    // se llenará desde JSON
let cartLists = [];                // carrito en memoria
let currentlySelectedKey = null;   // índice del plato en configuración
let fullList = [];                 // lista ordenada para rejilla
let currentFilter = 'todos';       // 'dia' | 'especial' | 'sopa' | 'todos'

// ───────────────────────── HELPERS DOM ────────────────────────────────
const $  = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

// ───────────────────────── INICIO ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  // 1) Asegurar UL.cart-list
  const cartContainer = $('.cart-container');
  let cartList = $('.cart-list');
  if (!cartList) {
    cartList = document.createElement('ul');
    cartList.className = 'cart-list list-group';
    cartContainer.appendChild(cartList);
  }

  // 2) Carga de productos y primera renderización
  await loadItems();
  renderGrid();

  // 3) Configurar filtros
  $('#btn-dia')     ?.addEventListener('click', e => { e.preventDefault(); currentFilter='dia';      renderGrid(); });
  $('#btn-especial')?.addEventListener('click', e => { e.preventDefault(); currentFilter='especial'; renderGrid(); });
  $('#btn-sopa')    ?.addEventListener('click', e => { e.preventDefault(); currentFilter='sopa';     renderGrid(); });
  $('#btn-todos')   ?.addEventListener('click', e => { e.preventDefault(); currentFilter='todos';   renderGrid(); });

  // 4) Carrito flotante
  $('#floating-cart').addEventListener('click', toggleCart);

  // 5) Delegación modal “¿Solo bandeja?”
  document.body.addEventListener('click', ev => {
    if (ev.target.id === 'yesButton') {
      // Plato del día con descuento: saltamos Sopas
      applySoloBandejaDiscount(currentlySelectedKey);
      closeModal('confirmationModal');
      showEntradaOptions();
    }
    if (ev.target.id === 'noButton') {
      // Menú completo: Sopas → Entrada → Ensalada
      addToCartList(currentlySelectedKey);
      closeModal('confirmationModal');
      showSoloSopaOptions();
    }
  });
});

// ───────────────────────── CARGAR PRODUCTOS ───────────────────────────
async function loadItems() {
  try {
    const res = await fetch('items.json', { cache: 'no-store' });
    if (!res.ok) throw new Error('items.json no disponible');
    items = await res.json();
  } catch (e) {
    console.error(e);
    if (window.productsFromPHP?.length) {
      items = window.productsFromPHP;
      console.warn('Usando JSON inyectado desde PHP');
    } else {
      return;
    }
  }

  // Clasificar y ordenar
  fullList = [];
  items.forEach((p, idx) => {
    if (p.name.startsWith('Entrada ') || p.name.startsWith('Ensalada ')) return;
    const tipo = p.name.startsWith('Especial ')   ? 'especial'
               : p.name.startsWith('Solo Sopa ')  ? 'sopa'
               : 'dia';
    const precio = (tipo === 'sopa') ? 8000 : Number(p.price);
    fullList.push({ idx, prod: p, tipo, precio });
  });
  fullList.sort((a,b) => ({ dia:0, especial:1, sopa:2 }[a.tipo] - ({ dia:0, especial:1, sopa:2 }[b.tipo])));
}

// ───────────────────────── RENDER REJILLA ──────────────────────────────
function renderGrid() {
  const grid = $('.products-grid');
  grid.innerHTML = '';
  fullList
    .filter(e => currentFilter === 'todos' || e.tipo === currentFilter)
    .forEach(({ idx, prod, precio }) => {
      grid.insertAdjacentHTML('beforeend', `
        <div class="product-card">
          <img src="${prod.image_path || prod.image}" class="product-image" alt="${prod.name}">
          <div class="product-details">
            <h3>${prod.name}</h3>
            <p class="price">$${precio.toLocaleString()}</p>
            <button class="btn btn-primary btn-block" onclick="addToCart(${idx})">
              <i class="fas fa-shopping-cart"></i> Ordenar
            </button>
          </div>
        </div>`);
    });
}

// ───────────────────────── CARRITO & MODALES ──────────────────────────
// Añadir o preguntar
function addToCart(key) {
  const sel = items[key];
  if (sel.name.startsWith('Especial ') || sel.name.startsWith('Solo Sopa ')) {
    addToCartList(key);
  } else {
    currentlySelectedKey = key;
    openModal('confirmationModal');
  }
}

// Agrega al carrito
function addToCartList(key) {
  const base = items[key];
  const isSoloSopa = base.name.startsWith('Solo Sopa ');
  const clone = {
    ...base,
    price: isSoloSopa ? 8000 : Number(base.price),
    quantity: 1
  };
  if (cartLists[key]) cartLists[key].quantity++;
  else cartLists[key] = clone;
  reloadCart(); updateCartCount();
}

// “Solo bandeja” descuento
function applySoloBandejaDiscount(key) {
  const base = items[key];
  const precio = Math.max(Number(base.price) - 2000, 0);
  const clone = {
    ...base,
    price: precio,
    quantity: 1,
    customOption: 'Solo bandeja'
  };
  if (cartLists[key]) cartLists[key].quantity++;
  else cartLists[key] = clone;
  reloadCart(); updateCartCount();
}

// Secuencia de complementos
function showSoloSopaOptions()  { renderOptions('Solo Sopa ','sopaOptionsModal',    'sopaOptionsContainer',    addSoloSopa); }
function showEntradaOptions()   { renderOptions('Entrada ','entradaOptionsModal', 'entradaOptionsContainer', addEntrada); }
function showEnsaladaOptions()  { renderOptions('Ensalada ','ensaladaOptionsModal','ensaladaOptionsContainer',addEnsalada); }

function addSoloSopa(idx)  { pickOption('Solo Sopa ', idx, 'sopaOption');    closeModal('sopaOptionsModal');    showEntradaOptions(); }
function addEntrada(idx)   { pickOption('Entrada ',   idx, 'entradaOption'); closeModal('entradaOptionsModal'); showEnsaladaOptions(); }
function addEnsalada(idx)  { pickOption('Ensalada ',  idx, 'ensaladaOption'); closeModal('ensaladaOptionsModal'); }

// Genera botones de opciones
function renderOptions(prefix, modalId, contId, cb) {
  const opts = items.filter(i => i.name.startsWith(prefix));
  $(`#${contId}`).innerHTML = opts.map((o,i)=>
    `<button class="option-btn" onclick="${cb.name}(${i})">${o.name.replace(prefix,'')}</button>`
  ).join('');
  openModal(modalId);
}

// Guarda la opción elegida
function pickOption(prefix, idx, field) {
  const opt = items.filter(i => i.name.startsWith(prefix))[idx];
  cartLists[currentlySelectedKey][field] = opt.name;
  reloadCart(); updateCartCount();
}

// Redibuja carrito
function reloadCart() {
  const cartList = $('.cart-list');
  const totalEl  = $('.total');
  if (!cartList || !totalEl) return;
  cartList.innerHTML = '';
  let total = 0;
  cartLists.forEach(v => {
    if (!v) return;
    const p = Number(v.price), q = v.quantity;
    total += p * q;
    const name = v.name +
      (v.customOption  ? ` (${v.customOption})` : '') +
      (v.sopaOption    ? ` con ${v.sopaOption}` : '') +
      (v.entradaOption ? `, ${v.entradaOption}` : '') +
      (v.ensaladaOption? ` y ${v.ensaladaOption}` : '');
    cartList.insertAdjacentHTML('beforeend', `
      <li class="list-group-item d-flex align-items-center">
        <img src="${v.image_path || v.image}" style="width:60px" class="mr-2">
        <div class="flex-fill">
          <h6 class="m-0">${name}</h6>
          <small>$${p.toLocaleString()}</small>
        </div>
        <div>
          <button onclick="changeQuantity(${v.id-1},${q-1})">-</button>
          <span>${q}</span>
          <button onclick="changeQuantity(${v.id-1},${q+1})">+</button>
        </div>
      </li>`);
  });
  totalEl.textContent = total.toLocaleString();
}

// Controles del carrito
function changeQuantity(k, q) { 
  if (q <= 0) delete cartLists[k]; else cartLists[k].quantity = q;
  reloadCart(); updateCartCount();
}
function clearCart()           { cartLists = []; reloadCart(); updateCartCount(); }
function updateCartCount()     { $('#cart-count').textContent = cartLists.filter(i=>i).length; }

// WhatsApp
function getCartText() {
  let txt = '', sum = 0;
  cartLists.forEach(i => {
    if (!i) return;
    const p = Number(i.price), q = i.quantity; sum += p*q;
    const name = i.name +
      (i.customOption  ? ` (${i.customOption})` : '') +
      (i.sopaOption    ? ` con ${i.sopaOption}` : '') +
      (i.entradaOption ? `, ${i.entradaOption}` : '') +
      (i.ensaladaOption? ` y ${i.ensaladaOption}`: '');
    txt += `*${name}* - $${p.toLocaleString()} x ${q} = $${(p*q).toLocaleString()}\n`;
  });
  txt += `\n*Total: $${sum.toLocaleString()}*`;
  return txt;
}
function sendWhatsApp() {
  if (!cartLists.length || cartLists.every(i => !i)) {
    alert('El carrito está vacío');
    return;
  }
  window.open(`https://api.whatsapp.com/send?phone=+573042702375&text=${encodeURIComponent(getCartText())}`, '_blank');
  toggleCart(); clearCart();
}

// Modales y carrito flotante
function openModal(id)    { $(`#${id}`).style.display = 'block'; }
function closeModal(id)   { $(`#${id}`).style.display = 'none'; }
function toggleCart() {
  const c = $('.cart-container'), b = $('#floating-cart');
  c.classList.toggle('active');
  b.innerHTML = c.classList.contains('active')
    ? '✖'
    : `🛒 <span id="cart-count">${$$('.cart-list li').length}</span>`;
}
