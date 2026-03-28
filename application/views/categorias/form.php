<?php
$es_editar = ($accion === 'editar' && !empty($categoria));
$action_url = $es_editar ? site_url('categorias/editar/' . $categoria->id) : site_url('categorias/crear');
$titulo_val = $es_editar ? $categoria->titulo : set_value('titulo');
?>
<?php $this->load->view('layouts/header'); ?>
<?php $this->load->view('layouts/sidebar'); ?>

<div class="md:ml-64 min-h-screen bg-slate-50 transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-10 w-full max-w-xl mx-auto">

        <header class="mb-8">
            <a href="<?= site_url('categorias') ?>" class="text-xs font-bold text-slate-400 hover:text-slate-600 mb-3 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Volver al listado
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                <?= $es_editar ? 'Editar categoría' : 'Nueva categoría' ?>
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                <?= $es_editar ? 'Actualiza título o reemplaza el logo.' : 'Título y logo obligatorios.' ?>
            </p>
        </header>

        <?php if (validation_errors()): ?>
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <?= validation_errors() ?>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                <?= $this->session->flashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <?= form_open_multipart($action_url, ['class' => 'space-y-6']) ?>

                <div>
                    <label for="titulo" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Título</label>
                    <input type="text" name="titulo" id="titulo" required maxlength="150"
                           value="<?= html_escape($titulo_val) ?>"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                           placeholder="Ej. Bebidas, Platos fuertes…">
                </div>

                <div>
                    <label for="logo" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                        Logo <?= $es_editar ? '(opcional — deja vacío para conservar el actual)' : '(obligatorio)' ?>
                    </label>
                    <?php if ($es_editar && !empty($categoria->logo)): ?>
                        <div class="mb-3 flex items-center gap-3">
                            <img src="<?= base_url($categoria->logo) ?>" alt="" class="h-16 w-16 rounded-xl object-cover border border-slate-200">
                            <span class="text-xs text-slate-500">Logo actual</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/gif,image/webp"
                           <?= $es_editar ? '' : 'required' ?>
                           class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-slate-800 cursor-pointer">
                    <p class="mt-2 text-[11px] text-slate-400">JPG, PNG, GIF o WebP. Máximo 2 MB.</p>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
                    <a href="<?= site_url('categorias') ?>"
                       class="inline-flex justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-900/20 hover:bg-slate-800 transition">
                        <?= $es_editar ? 'Guardar cambios' : 'Crear categoría' ?>
                    </button>
                </div>

            <?= form_close() ?>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
