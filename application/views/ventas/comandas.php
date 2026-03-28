<?php $this->load->view('layouts/header'); ?>
<?php $this->load->view('layouts/sidebar'); ?>

<div class="md:ml-64 min-h-screen bg-slate-50 transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-10 w-full max-w-6xl mx-auto">

        <header class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 border-b border-slate-200 pb-6">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Ventas</p>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Comandas del día</h1>
                <p class="text-sm text-slate-500 mt-1">Pedidos registrados en el POS, vinculados a mesas y productos.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?= site_url('ventas/pos') ?>"
                   class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-800">
                    <i class="fas fa-cash-register mr-2 text-amber-400"></i> Ir al POS
                </a>
            </div>
        </header>

        <?php if (!$pos_ok): ?>
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                Ejecuta <code class="bg-amber-100 px-1 rounded text-xs">application/sql/restaurant_gestion.sql</code> para ver comandas reales.
            </div>
        <?php endif; ?>

        <form method="get" action="<?= site_url('ventas/comandas') ?>" class="mb-6 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Fecha</label>
                <input type="date" name="fecha" value="<?= html_escape($fecha) ?>"
                       class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold">
            </div>
            <button type="submit" class="rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                Ver
            </button>
        </form>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider">#</th>
                            <th class="px-5 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider">Mesa</th>
                            <th class="px-5 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider">Tipo</th>
                            <th class="px-5 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider">Estado</th>
                            <th class="px-5 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider text-right">Total</th>
                            <th class="px-5 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider">Hora</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($pedidos)): ?>
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-500">No hay pedidos en esta fecha.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pedidos as $p): ?>
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-5 py-3 font-mono font-bold text-slate-800"><?= (int) $p->id ?></td>
                                    <td class="px-5 py-3 text-slate-700">
                                        <?= $p->mesa_codigo ? html_escape($p->mesa_codigo . ' — ' . $p->mesa_nombre) : '—' ?>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600"><?= html_escape($p->tipo) ?></td>
                                    <td class="px-5 py-3">
                                        <?php
                                        $est = $p->estado;
                                        $cls = 'bg-slate-100 text-slate-700';
                                        if ($est === 'abierto') {
                                            $cls = 'bg-amber-100 text-amber-800';
                                        }
                                        if ($est === 'cuenta_cerrada') {
                                            $cls = 'bg-emerald-100 text-emerald-800';
                                        }
                                        if ($est === 'anulado') {
                                            $cls = 'bg-red-100 text-red-800';
                                        }
                                        ?>
                                        <span class="inline-flex rounded-lg px-2 py-1 text-[10px] font-bold uppercase <?= $cls ?>">
                                            <?= html_escape($est) ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right font-black text-slate-900">S/ <?= number_format((float) $p->total, 2) ?></td>
                                    <td class="px-5 py-3 text-slate-500 text-xs"><?= html_escape($p->created_at) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
