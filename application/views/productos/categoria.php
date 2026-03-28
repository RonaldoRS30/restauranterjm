<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$q_val = isset($q) ? $q : '';
?>
<?php $this->load->view('layouts/header'); ?>
<?php $this->load->view('layouts/sidebar'); ?>

<div class="md:ml-64 min-h-screen bg-slate-50 transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-10 w-full max-w-6xl mx-auto">

        <a href="<?= site_url('productos') ?>"
           class="text-xs font-bold text-slate-400 hover:text-slate-600 mb-6 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Todas las categorías
        </a>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                <?= $this->session->flashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                <?= $this->session->flashdata('error') ?>
            </div>
        <?php endif; ?>

        <header class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between mb-8 pb-8 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-start gap-6 min-w-0">
                <div class="flex-shrink-0">
                    <?php if (!empty($categoria->logo)): ?>
                        <img src="<?= base_url($categoria->logo) ?>" alt=""
                             class="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl object-cover border border-slate-200 shadow-sm">
                    <?php else: ?>
                        <div class="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center text-slate-500">
                            <i class="fas fa-folder text-3xl"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Categoría</p>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        <?= html_escape($categoria->titulo) ?>
                    </h1>
                    <p class="text-xs font-mono text-slate-400 mt-2">slug: <?= html_escape($categoria->slug) ?> · id: <?= (int) $categoria->id ?></p>
                    <p class="text-sm text-slate-500 mt-3">
                        <strong><?= (int) $productos_count ?></strong> producto(s) activo(s) en la categoría.
                        Listado paginado con búsqueda, edición y baja lógica.
                    </p>
                </div>
            </div>
            <a href="<?= site_url('productos/agregar/' . (int) $categoria->id) ?>"
               class="inline-flex flex-shrink-0 items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-900/20 hover:bg-slate-800 transition self-start">
                <i class="fas fa-plus mr-2 text-amber-400"></i>
                Agregar producto
            </a>
        </header>

        <form method="get" action="<?= site_url('productos/categoria/' . (int) $categoria->id) ?>"
              class="mb-6 flex flex-col sm:flex-row gap-3 sm:items-end">
            <div class="flex-1 max-w-md">
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Buscar por nombre</label>
                <div class="flex gap-2">
                    <input type="search" name="q" value="<?= html_escape($q_val) ?>"
                           class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm"
                           placeholder="Ej. ceviche, chicha…">
                    <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white">Buscar</button>
                </div>
            </div>
        </form>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center">
                    <i class="fas fa-cubes mr-2 text-amber-500"></i>
                    Productos
                </h2>
            </div>

            <?php if (empty($productos)): ?>
                <p class="text-sm text-slate-500 py-10 text-center border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                    No hay resultados<?= $q_val !== '' ? ' para tu búsqueda' : '' ?>.
                    <span class="block mt-3">
                        <a href="<?= site_url('productos/agregar/' . (int) $categoria->id) ?>"
                           class="inline-flex items-center text-sm font-bold text-blue-600 hover:text-blue-800">
                            <i class="fas fa-plus-circle mr-2"></i> Agregar producto
                        </a>
                    </span>
                </p>
            <?php else: ?>
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="min-w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-4 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider">Nombre</th>
                                <th class="px-4 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider text-right w-24">Stock</th>
                                <th class="px-4 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider text-right w-28">Costo</th>
                                <th class="px-4 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider text-right w-28">Venta</th>
                                <th class="px-4 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($productos as $p): ?>
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-4 py-3 font-semibold text-slate-900"><?= html_escape($p->nombre) ?></td>
                                    <td class="px-4 py-3 text-right font-mono text-slate-700"><?= (int) ($p->stock ?? 0) ?></td>
                                    <td class="px-4 py-3 text-right font-mono text-slate-600">
                                        <?= $p->precio_costo !== null ? 'S/ ' . number_format((float) $p->precio_costo, 2) : '—' ?>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                        <?= $p->precio_venta !== null ? 'S/ ' . number_format((float) $p->precio_venta, 2) : '—' ?>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="<?= site_url('productos/editar/' . (int) $p->id) ?>"
                                           class="text-xs font-bold text-blue-600 hover:text-blue-800 mr-3">Editar</a>
                                        <a href="<?= site_url('productos/eliminar/' . (int) $p->id) ?>"
                                           onclick="return confirm('¿Desactivar este producto? Ya no aparecerá en el POS.');"
                                           class="text-xs font-bold text-red-600 hover:text-red-800">Desactivar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if (!empty($pagination_links)): ?>
                <?= $pagination_links ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
