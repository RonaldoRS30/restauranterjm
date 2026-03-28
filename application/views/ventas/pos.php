<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$b_mesas = [];
foreach ($mesas as $m) {
    $b_mesas[] = [
        'id'     => (int) $m->id,
        'codigo' => $m->codigo,
        'nombre' => $m->nombre,
        'estado' => $m->estado,
    ];
}

$b_lineas = [];
foreach ($lineas as $ln) {
    $b_lineas[] = [
        'id'               => (int) $ln->id,
        'producto_nombre'  => $ln->producto_nombre,
        'cantidad'         => (float) $ln->cantidad,
        'precio_unitario'  => (float) $ln->precio_unitario,
        'subtotal_linea'   => (float) $ln->subtotal_linea,
    ];
}

$b_pedido = null;
if ($pedido) {
    $b_pedido = [
        'id'             => (int) $pedido->id,
        'subtotal'       => (float) $pedido->subtotal,
        'servicio_pct'   => (float) $pedido->servicio_pct,
        'servicio_monto' => (float) $pedido->servicio_monto,
        'total'          => (float) $pedido->total,
        'estado'         => $pedido->estado,
    ];
}

$bootstrap = [
    'mesas'           => $b_mesas,
    'categorias'      => $categorias_pos,
    'mesaIdInicial'   => ($mesa_activa && (int) $mesa_id_get > 0) ? (int) $mesa_activa->id : 0,
    'pedido'          => $b_pedido,
    'lineas'          => $b_lineas,
    'posOk'           => (bool) $pos_ok,
    'urls'            => [
        'abrir'    => site_url('ventas/api_abrir_pedido'),
        'linea'    => site_url('ventas/api_agregar_linea'),
        'lineaQty' => site_url('ventas/api_actualizar_linea'),
        'lineaDel' => site_url('ventas/api_eliminar_linea'),
        'servicio' => site_url('ventas/api_servicio'),
        'cerrar'   => site_url('ventas/api_cerrar'),
        'anular'   => site_url('ventas/api_anular'),
    ],
];
$bootstrap_json = json_encode($bootstrap, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>
<?php $this->load->view('layouts/header'); ?>
<?php $this->load->view('layouts/sidebar'); ?>

<div class="md:ml-64 min-h-screen bg-slate-100 transition-all duration-300"
     x-data="posApp()"
     x-init="init()">

    <script type="application/json" id="pos-bootstrap"><?= $bootstrap_json ?></script>

    <div class="flex flex-col min-h-screen lg:min-h-[calc(100vh-0px)]">

        <header class="flex-shrink-0 border-b border-slate-200 bg-white px-4 py-3 sm:px-6 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Punto de venta</p>
                    <h1 class="text-lg font-black text-slate-900 sm:text-xl">Comandas — restaurante</h1>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="<?= site_url('productos') ?>"
                       class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100">
                        <i class="fas fa-tags mr-2 text-amber-600"></i> Productos
                    </a>
                    <a href="<?= site_url('categorias') ?>"
                       class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100">
                        <i class="fas fa-layer-group mr-2 text-blue-600"></i> Categorías
                    </a>
                    <a href="<?= site_url('ventas/comandas') ?>"
                       class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100">
                        <i class="fas fa-receipt mr-2 text-emerald-600"></i> Comandas del día
                    </a>
                    <span class="inline-flex items-center rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white">
                        <i class="far fa-user mr-2 text-amber-400"></i>
                        <?= html_escape($usuario_nombre) ?>
                    </span>
                </div>
            </div>
        </header>

        <?php if (!$pos_ok): ?>
            <div class="mx-4 mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                <strong class="font-bold">Instalación pendiente.</strong> Ejecuta <code class="bg-amber-100 px-1 rounded text-xs">application/sql/restaurant_gestion.sql</code> para crear mesas y pedidos. Mientras tanto puedes ver el menú pero no guardar comandas.
            </div>
        <?php endif; ?>

        <!-- Selector mesa + tipo -->
        <div class="flex-shrink-0 border-b border-slate-200 bg-white px-4 py-3 sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
                <div class="min-w-[200px]">
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Mesa / punto</label>
                    <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-800"
                            @change="irMesa($event.target.value)">
                        <option value="">— Elegir —</option>
                        <?php foreach ($mesas as $m): ?>
                            <option value="<?= (int) $m->id ?>" <?= ((int) $mesa_id_get === (int) $m->id) ? 'selected' : '' ?>>
                                <?= html_escape($m->codigo) ?> — <?= html_escape($m->nombre) ?>
                                <?= $m->estado === 'ocupada' ? ' (ocupada)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="min-w-[160px]" x-show="mesaId && posOk">
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Tipo atención</label>
                    <select x-model="tipoAtencion" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                        <option value="salon">Salón</option>
                        <option value="para_llevar">Para llevar</option>
                        <option value="delivery">Delivery</option>
                        <option value="barra">Barra</option>
                    </select>
                </div>
                <button type="button" x-show="mesaId && posOk && !pedido"
                        @click="abrirCuenta()"
                        :disabled="busy"
                        class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-black text-white shadow-md hover:bg-emerald-700 disabled:opacity-50">
                    Abrir cuenta
                </button>
                <button type="button" x-show="mesaId && posOk && pedido && lineas.length === 0"
                        @click="anularPedido()"
                        :disabled="busy"
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-bold text-red-700 hover:bg-red-100">
                    Anular cuenta vacía
                </button>
            </div>
        </div>

        <div class="flex flex-1 flex-col overflow-hidden lg:flex-row min-h-0">
            <section class="flex min-h-0 flex-1 flex-col border-b border-slate-200 bg-slate-50 lg:border-b-0 lg:border-r">
                <div class="flex-shrink-0 border-b border-slate-200 bg-white px-3 py-3 sm:px-4">
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        <button type="button" @click="filtroCat = 'all'"
                                :class="filtroCat === 'all' ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-600'"
                                class="flex-shrink-0 rounded-full px-4 py-2 text-xs font-bold">Todos</button>
                        <template x-for="c in categorias" :key="c.id">
                            <button type="button" @click="filtroCat = c.id"
                                    :class="filtroCat === c.id ? 'bg-amber-600 text-white' : 'border border-slate-200 bg-white text-slate-600'"
                                    class="flex-shrink-0 rounded-full px-4 py-2 text-xs font-bold"
                                    x-text="c.titulo"></button>
                        </template>
                    </div>
                </div>
                <div class="flex-shrink-0 px-3 py-3 sm:px-4">
                    <div class="relative">
                        <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="search" x-model="busqueda"
                               class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm shadow-sm"
                               placeholder="Buscar plato…">
                    </div>
                </div>
                <div class="min-h-0 flex-1 overflow-y-auto px-3 pb-4 sm:px-4">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                        <template x-for="item in productosFiltrados()" :key="item.id">
                            <button type="button"
                                    @click="agregarProducto(item)"
                                    :disabled="!posOk || !pedido || busy || item.precio <= 0"
                                    class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white text-left shadow-sm transition hover:border-amber-300 hover:shadow-md disabled:opacity-40 disabled:cursor-not-allowed">
                                <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-amber-50 to-orange-50">
                                    <i class="fas fa-utensils text-3xl text-amber-600/30"></i>
                                </div>
                                <div class="flex flex-1 flex-col p-3">
                                    <h3 class="text-sm font-bold text-slate-900 leading-tight" x-text="item.nombre"></h3>
                                    <p class="mt-auto pt-2 text-base font-black text-slate-900">
                                        S/ <span x-text="item.precio.toFixed(2)"></span>
                                    </p>
                                </div>
                            </button>
                        </template>
                    </div>
                    <p x-show="productosFiltrados().length === 0" class="text-center text-sm text-slate-500 py-12">
                        No hay productos con ese criterio. Revisa categorías y precios de venta en <a href="<?= site_url('productos') ?>" class="font-bold text-blue-600">Productos</a>.
                    </p>
                </div>
            </section>

            <aside class="flex w-full flex-col border-t border-slate-200 bg-white lg:w-[380px] lg:flex-shrink-0 lg:border-l lg:border-t-0 xl:w-[420px]">
                <div class="flex-shrink-0 border-b border-slate-100 px-4 py-4">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Comanda</p>
                            <p class="text-sm font-black text-slate-900" x-show="pedido">
                                #<span x-text="pedido ? pedido.id : ''"></span>
                                <span class="text-slate-500 font-semibold text-xs" x-text="mesaNombre()"></span>
                            </p>
                            <p class="text-sm text-slate-500" x-show="!pedido">Sin cuenta abierta</p>
                        </div>
                        <span x-show="pedido" class="rounded-lg bg-amber-100 px-2 py-1 text-[10px] font-bold uppercase text-amber-800">Abierta</span>
                    </div>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto px-4 py-3">
                    <ul class="space-y-3">
                        <template x-for="ln in lineas" :key="ln.id">
                            <li class="flex gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                                <input type="number" min="0.5" step="0.5" :value="ln.cantidad"
                                       @change="cambiarQty(ln.id, $event.target.value)"
                                       class="h-9 w-14 flex-shrink-0 rounded-lg border border-slate-200 text-center text-sm font-black">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-slate-900" x-text="ln.producto_nombre"></p>
                                    <p class="text-xs text-slate-500">S/ <span x-text="ln.precio_unitario.toFixed(2)"></span> c/u</p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <p class="text-sm font-black text-slate-900">S/ <span x-text="ln.subtotal_linea.toFixed(2)"></span></p>
                                    <button type="button" @click="eliminarLinea(ln.id)" class="text-[10px] font-bold text-red-500 hover:text-red-700">Quitar</button>
                                </div>
                            </li>
                        </template>
                    </ul>
                    <p x-show="pedido && lineas.length === 0" class="text-sm text-slate-400 text-center py-8">Añade productos desde el menú.</p>
                </div>

                <div class="flex-shrink-0 border-t border-slate-100 bg-slate-50 px-4 py-4" x-show="pedido">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <label class="text-xs font-bold text-slate-500">Servicio %</label>
                        <input type="number" min="0" max="100" step="1" :value="pedido ? pedido.servicio_pct : 10"
                               @change="actualizarServicio($event.target.value)"
                               class="w-20 rounded-lg border border-slate-200 px-2 py-1 text-sm font-bold text-right">
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-800" x-text="pedido ? 'S/ ' + pedido.subtotal.toFixed(2) : '—'"></span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Servicio</span>
                            <span class="font-semibold text-slate-800" x-text="pedido ? 'S/ ' + pedido.servicio_monto.toFixed(2) : '—'"></span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-black text-slate-900">
                            <span>Total</span>
                            <span x-text="pedido ? 'S/ ' + pedido.total.toFixed(2) : '—'"></span>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-2">
                        <button type="button" @click="cerrarCuenta()"
                                :disabled="busy || !lineas.length"
                                class="rounded-xl bg-emerald-600 py-3.5 text-sm font-black text-white shadow-lg shadow-emerald-600/25 disabled:opacity-40">
                            <i class="fas fa-check-circle mr-2"></i> Cobrar y cerrar (descuenta stock)
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<script>
function posApp() {
    return {
        mesas: [],
        categorias: [],
        mesaId: 0,
        filtroCat: 'all',
        busqueda: '',
        pedido: null,
        lineas: [],
        urls: {},
        posOk: false,
        tipoAtencion: 'salon',
        busy: false,
        init() {
            const el = document.getElementById('pos-bootstrap');
            const raw = JSON.parse(el.textContent);
            this.mesas = raw.mesas || [];
            this.categorias = raw.categorias || [];
            this.mesaId = raw.mesaIdInicial || 0;
            this.pedido = raw.pedido;
            this.lineas = raw.lineas || [];
            this.urls = raw.urls || {};
            this.posOk = raw.posOk;
        },
        irMesa(id) {
            const v = parseInt(id, 10) || 0;
            const u = '<?= site_url('ventas/pos') ?>';
            if (v < 1) { window.location.href = u; return; }
            window.location.href = u + '?mesa_id=' + v;
        },
        mesaNombre() {
            const m = this.mesas.find(x => x.id === this.mesaId);
            return m ? ' · ' + m.codigo : '';
        },
        productosFiltrados() {
            let list = [];
            const q = (this.busqueda || '').toLowerCase().trim();
            for (const c of this.categorias) {
                if (this.filtroCat !== 'all' && this.filtroCat !== c.id) continue;
                for (const p of (c.productos || [])) {
                    if (q && !p.nombre.toLowerCase().includes(q)) continue;
                    list.push({ ...p, catId: c.id });
                }
            }
            return list;
        },
        async post(url, body) {
            const fd = new FormData();
            for (const k in body) fd.append(k, body[k]);
            const r = await fetch(url, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            return r.json();
        },
        async abrirCuenta() {
            if (!this.posOk || !this.mesaId) return;
            this.busy = true;
            try {
                const j = await this.post(this.urls.abrir, { mesa_id: this.mesaId, tipo: this.tipoAtencion });
                if (j.ok) location.reload();
                else alert(j.msg || 'Error');
            } finally { this.busy = false; }
        },
        async agregarProducto(item) {
            if (!this.pedido || item.precio <= 0) return;
            this.busy = true;
            try {
                const j = await this.post(this.urls.linea, { pedido_id: this.pedido.id, producto_id: item.id, cantidad: 1 });
                if (j.ok && j.payload) { this.pedido = j.payload.pedido; this.lineas = j.payload.lineas; }
                else alert(j.msg || 'Error');
            } finally { this.busy = false; }
        },
        async cambiarQty(id, val) {
            const cantidad = parseFloat(val);
            if (!(cantidad > 0)) return;
            this.busy = true;
            try {
                const j = await this.post(this.urls.lineaQty, { detalle_id: id, cantidad: cantidad });
                if (j.ok && j.payload) { this.pedido = j.payload.pedido; this.lineas = j.payload.lineas; }
                else alert(j.msg || 'Error');
            } finally { this.busy = false; }
        },
        async eliminarLinea(id) {
            this.busy = true;
            try {
                const j = await this.post(this.urls.lineaDel, { detalle_id: id });
                if (j.ok && j.payload) { this.pedido = j.payload.pedido; this.lineas = j.payload.lineas; }
                else alert(j.msg || 'Error');
            } finally { this.busy = false; }
        },
        async actualizarServicio(pct) {
            if (!this.pedido) return;
            this.busy = true;
            try {
                const j = await this.post(this.urls.servicio, { pedido_id: this.pedido.id, servicio_pct: pct });
                if (j.ok && j.payload) { this.pedido = j.payload.pedido; this.lineas = j.payload.lineas; }
            } finally { this.busy = false; }
        },
        async cerrarCuenta() {
            if (!this.pedido || !confirm('¿Cerrar cuenta y descontar stock?')) return;
            this.busy = true;
            try {
                const j = await this.post(this.urls.cerrar, { pedido_id: this.pedido.id });
                if (j.ok) { alert(j.msg || 'Listo'); location.reload(); }
                else alert(j.msg || 'Error');
            } finally { this.busy = false; }
        },
        async anularPedido() {
            if (!this.pedido || !confirm('¿Anular esta cuenta vacía?')) return;
            this.busy = true;
            try {
                const j = await this.post(this.urls.anular, { pedido_id: this.pedido.id });
                if (j.ok) {
                    location.reload();
                } else {
                    alert(j.msg || 'No se pudo anular');
                }
            } finally { this.busy = false; }
        }
    };
}
</script>

<?php $this->load->view('layouts/footer'); ?>
