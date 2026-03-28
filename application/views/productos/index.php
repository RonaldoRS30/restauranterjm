<?php $this->load->view('layouts/header'); ?>
<?php $this->load->view('layouts/sidebar'); ?>

<div class="md:ml-64 min-h-screen bg-slate-50 transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-10 w-full max-w-6xl mx-auto">

        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                <?= $this->session->flashdata('error') ?>
            </div>
        <?php endif; ?>

        <header class="mb-10 border-b border-slate-200 pb-6">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Productos</p>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Base por categoría</h1>
            <p class="text-sm text-slate-500 mt-2 max-w-2xl">
                Elige una categoría para entrar a su módulo. Cada una podrá tener lógica distinta (listados, formularios, reglas).
                Las categorías y sus logos se administran en <strong>Categorías</strong>.
            </p>
        </header>

        <?php if (empty($categorias)): ?>
            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white px-8 py-16 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <i class="fas fa-folder-open text-2xl"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-800">No hay categorías activas</h2>
                <p class="text-sm text-slate-500 mt-2 mb-6">Crea categorías con título y logo para verlas aquí como accesos directos.</p>
                <a href="<?= site_url('categorias/crear') ?>"
                   class="inline-flex items-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white hover:bg-slate-800 transition">
                    <i class="fas fa-plus mr-2 text-amber-400"></i>
                    Ir a nueva categoría
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                <?php foreach ($categorias as $cat): ?>
                    <a href="<?= site_url('productos/categoria/' . $cat->id) ?>"
                       class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-amber-300 hover:shadow-md hover:-translate-y-0.5">
                        <div class="aspect-square bg-slate-100 overflow-hidden">
                            <?php if (!empty($cat->logo)): ?>
                                <img src="<?= base_url($cat->logo) ?>" alt=""
                                     class="h-full w-full object-cover transition group-hover:scale-105 duration-300">
                            <?php else: ?>
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-200 to-slate-300 text-slate-500">
                                    <i class="fas fa-tags text-4xl opacity-50"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-4 border-t border-slate-100">
                            <h2 class="text-sm font-black text-slate-900 leading-tight group-hover:text-amber-700 transition">
                                <?= html_escape($cat->titulo) ?>
                            </h2>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-2 flex items-center">
                                Abrir módulo
                                <i class="fas fa-chevron-right ml-1 text-[9px] opacity-0 group-hover:opacity-100 transition"></i>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
