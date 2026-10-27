// front/dist/js/api.js
// Cliente mínimo para hablar con los endpoints PHP (/php/*.php).
const API = {
    async request(endpoint, method = 'GET', data = null) {
        const opciones = {
            method,
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin' // mantiene la cookie de sesión PHP
        };
        if (data && method !== 'GET') opciones.body = JSON.stringify(data);
        try {
            const r = await fetch(endpoint, opciones);
            return await r.json();
        } catch (e) {
            console.error('Error de conexión con la API:', e);
            return { status: 'error', message: 'No se pudo conectar con el servidor.' };
        }
    },

    // ---- Autenticación ----
    login: (email, password, rol) => API.request('/php/auth.php?accion=login', 'POST', { email, password, rol }),
    register: (datos) => API.request('/php/auth.php?accion=register', 'POST', datos),
    logout: () => API.request('/php/auth.php?accion=logout', 'POST', {}),
    sesion: () => API.request('/php/auth.php?accion=sesion'),

    // ---- Catálogo público ----
    catalogo: () => API.request('/php/catalogo.php'),

    // ---- Panel de administrador ----
    adminResumen: () => API.request('/php/admin.php?accion=resumen'),
    adminArticulos: () => API.request('/php/admin.php?accion=articulos'),
    adminUsuarios: () => API.request('/php/admin.php?accion=usuarios'),

    // ---- Biblioteca del usuario (catálogo completo, favoritos, pedidos) ----
    biblioCatalogo: () => API.request('/php/biblioteca.php?accion=catalogo'),
    biblioFavorito: (id) => API.request('/php/biblioteca.php?accion=favorito', 'POST', { id }),
    biblioComprar: (id) => API.request('/php/biblioteca.php?accion=comprar', 'POST', { id }),
    biblioListas: () => API.request('/php/biblioteca.php?accion=listas'),

    // ---- Perfil ----
    perfilActualizar: (datos) => API.request('/php/perfil.php?accion=actualizar', 'POST', datos),
    perfilPassword: (datos) => API.request('/php/perfil.php?accion=password', 'POST', datos)
};

// Escapa texto antes de meterlo en innerHTML (evita XSS)
function esc(t) {
    return String(t ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

// ------------------------------------------------------------
// Ojito para ver/ocultar la contraseña en TODOS los input[type=password]
// ------------------------------------------------------------
function activarOjos() {
    if (!document.getElementById('estilo-ojo')) {
        const st = document.createElement('style');
        st.id = 'estilo-ojo';
        st.textContent = '.campo-pass{position:relative}.campo-pass>input{padding-right:2.8rem !important}' +
            '.btn-ojo{position:absolute;right:6px;top:50%;transform:translateY(-50%);border:0;background:none;color:#6c757d;' +
            'padding:6px 9px;font-size:18px;line-height:1;cursor:pointer;border-radius:8px}.btn-ojo:hover{color:#0d6efd}';
        document.head.appendChild(st);
    }
    document.querySelectorAll('input[type="password"]').forEach(inp => {
        if (inp.dataset.ojo) return;
        inp.dataset.ojo = '1';
        const caja = document.createElement('div');
        caja.className = 'campo-pass';
        inp.parentNode.insertBefore(caja, inp);
        caja.appendChild(inp);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn-ojo';
        btn.setAttribute('aria-label', 'Mostrar u ocultar contraseña');
        btn.innerHTML = '<i class="bi bi-eye"></i>';
        btn.addEventListener('click', () => {
            const ver = inp.type === 'password';
            inp.type = ver ? 'text' : 'password';
            btn.innerHTML = ver ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
        caja.appendChild(btn);
    });
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', activarOjos);
else activarOjos();
