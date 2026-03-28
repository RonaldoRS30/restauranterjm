<?php $this->load->view('layouts/header'); ?>
<?php $this->load->view('layouts/sidebar'); ?>

<div class="md:ml-64 min-h-screen bg-slate-50 transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-10 w-full max-w-lg mx-auto">

        <a href="<?= site_url('productos/categoria/' . (int) $producto->id_categoria) ?>"
           class="text-xs font-bold text-slate-400 hover:text-slate-600 mb-4 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver a <?= html_escape($producto->categoria_titulo) ?>
        </a>

        <header class="mb-6">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Editar producto</p>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight"><?= html_escape($producto->nombre) ?></h1>
        </header>

        <?php if (validation_errors()): ?>
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <?= validation_errors() ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <?= form_open('productos/editar/' . (int) $producto->id, ['class' => 'space-y-5']) ?>

                <div>
                    <label for="nombre" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nombre</label>
                    <input type="text" name="nombre" id="nombre" required maxlength="200"
                           value="<?= set_value('nombre', $producto->nombre) ?>"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition">
                </div>

                <div>
                    <label for="stock" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Stock</label>
                    <input type="number" name="stock" id="stock" required min="0" step="1"
                           value="<?= set_value('stock', (int) $producto->stock) ?>"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition">
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Precios (POS usa precio venta)</p>
                    <div class="space-y-4">
                        <div>
                            <label for="precio_costo" class="block text-xs font-semibold text-slate-600 mb-1">Precio costo</label>
                            <input type="text" name="precio_costo" id="precio_costo" inputmode="decimal"
                                   value="<?= set_value('precio_costo', $producto->precio_costo !== null ? number_format((float) $producto->precio_costo, 2, '.', '') : '') ?>"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                                   placeholder="Opcional">
                        </div>
                        <div>
                            <label for="precio_venta" class="block text-xs font-semibold text-slate-600 mb-1">Precio venta</label>
                            <input type="text" name="precio_venta" id="precio_venta" inputmode="decimal"
                                   value="<?= set_value('precio_venta', $producto->precio_venta !== null ? number_format((float) $producto->precio_venta, 2, '.', '') : '') ?>"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                                   placeholder="Para carta y POS">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4">
                    <a href="<?= site_url('productos/categoria/' . (int) $producto->id_categoria) ?>"
                       class="inline-flex justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-900/20 hover:bg-slate-800 transition">
                        <i class="fas fa-save mr-2 text-amber-400"></i>
                        Guardar cambios
                    </button>
                </div>

            <?= form_close() ?>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
