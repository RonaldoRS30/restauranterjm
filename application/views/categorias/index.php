<?php $this->load->view('layouts/header'); ?>
<?php $this->load->view('layouts/sidebar'); ?>

<div class="md:ml-64 min-h-screen bg-slate-50 transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-10 w-full max-w-6xl mx-auto">

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

        <header class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 border-b border-slate-200 pb-6">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Catálogo</p>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Categorías</h1>
                <p class="text-sm text-slate-500 mt-1">Título y logo por categoría; se muestran como tarjetas en Productos.</p>
            </div>
            <a href="<?= site_url('categorias/crear') ?>"
               class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-900/20 hover:bg-slate-800 transition">
                <i class="fas fa-plus mr-2 text-amber-400"></i>
                Nueva categoría
            </a>
        </header>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80">
                            <th class="px-5 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider">Logo</th>
                            <th class="px-5 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider">Título</th>
                            <th class="px-5 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider hidden md:table-cell">Slug</th>
                            <th class="px-5 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider">Estado</th>
                            <th class="px-5 py-3 font-bold text-slate-500 uppercase text-[10px] tracking-wider text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($categorias)): ?>
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                                    No hay categorías. Crea la primera para verla en <strong>Productos</strong>.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categorias as $c): ?>
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-3">
                                        <?php if (!empty($c->logo)): ?>
                                            <img src="<?= base_url($c->logo) ?>" alt=""
                                                 class="h-12 w-12 rounded-xl object-cover border border-slate-200 bg-slate-100">
                                        <?php else: ?>
                                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center text-slate-500">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 font-semibold text-slate-900"><?= html_escape($c->titulo) ?></td>
                                    <td class="px-5 py-3 text-slate-500 font-mono text-xs hidden md:table-cell"><?= html_escape($c->slug) ?></td>
                                    <td class="px-5 py-3">
                                        <?php if ((int) $c->estado === 1): ?>
                                            <span class="inline-flex rounded-lg bg-emerald-100 px-2 py-1 text-[10px] font-bold uppercase text-emerald-800">Activa</span>
                                        <?php else: ?>
                                            <span class="inline-flex rounded-lg bg-slate-200 px-2 py-1 text-[10px] font-bold uppercase text-slate-600">Inactiva</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 text-right whitespace-nowrap">
                                        <a href="<?= site_url('categorias/editar/' . $c->id) ?>"
                                           class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 mr-3">
                                            Editar
                                        </a>
                                        <?php if ((int) $c->estado === 1): ?>
                                            <a href="<?= site_url('categorias/eliminar/' . $c->id) ?>"
                                               onclick="return confirm('¿Desactivar esta categoría?');"
                                               class="inline-flex items-center text-xs font-bold text-amber-600 hover:text-amber-800 mr-3">
                                                Desactivar
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= site_url('categorias/activar/' . $c->id) ?>"
                                               class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-800">
                                                Activar
                                            </a>
                                        <?php endif; ?>
                                    </td>
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
