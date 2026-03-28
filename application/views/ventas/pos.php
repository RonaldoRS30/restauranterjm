<?php $this->load->view('layouts/header'); ?>
<?php $this->load->view('layouts/sidebar'); ?>

<div class="md:ml-64 min-h-screen bg-slate-100 transition-all duration-300">
    <div class="flex flex-col h-[calc(100vh-0px)] lg:min-h-screen">

        <!-- Barra superior POS -->
        <header class="flex-shrink-0 border-b border-slate-200 bg-white px-4 py-3 sm:px-6 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Punto de venta</p>
                    <h1 class="text-lg font-black text-slate-900 sm:text-xl">Restaurante — vista previa</h1>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <span class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600">
                        <i class="fas fa-utensils mr-2 text-amber-600"></i>
                        Salón
                    </span>
                    <span class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600">
                        <i class="fas fa-chair mr-2 text-slate-400"></i>
                        Mesa 4
                    </span>
                    <span class="inline-flex items-center rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white shadow-md">
                        <i class="far fa-user mr-2 text-amber-400"></i>
                        Cajero demo
                    </span>
                </div>
            </div>
        </header>

        <!-- Contenido principal: menú + cuenta -->
        <div class="flex flex-1 flex-col overflow-hidden lg:flex-row">
            <!-- Panel menú -->
            <section class="flex min-h-0 flex-1 flex-col border-b border-slate-200 bg-slate-50 lg:border-b-0 lg:border-r">
                <!-- Categorías (estático) -->
                <div class="flex-shrink-0 border-b border-slate-200 bg-white px-3 py-3 sm:px-4">
                    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin">
                        <button type="button" class="flex-shrink-0 rounded-full bg-slate-900 px-4 py-2 text-xs font-bold text-white shadow-sm">Todos</button>
                        <button type="button" class="flex-shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600">Entradas</button>
                        <button type="button" class="flex-shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600">Platos fuertes</button>
                        <button type="button" class="flex-shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600">Bebidas</button>
                        <button type="button" class="flex-shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600">Postres</button>
                        <button type="button" class="flex-shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600">Menú niños</button>
                    </div>
                </div>

                <!-- Búsqueda (decorativa) -->
                <div class="flex-shrink-0 px-3 py-3 sm:px-4">
                    <div class="relative">
                        <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="search" readonly
                            class="w-full cursor-default rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-500 shadow-sm"
                            placeholder="Buscar plato o código… (demo)"
                            value="">
                    </div>
                </div>

                <!-- Grilla de ítems -->
                <div class="min-h-0 flex-1 overflow-y-auto px-3 pb-4 sm:px-4">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">

                        <!-- Tarjeta ítem estática (repetimos variaciones) -->
                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-amber-300 hover:shadow-md">
                            <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-amber-100 to-orange-50">
                                <i class="fas fa-drumstick-bite text-4xl text-amber-600/40"></i>
                            </div>
                            <div class="flex flex-1 flex-col p-3">
                                <h3 class="text-sm font-bold text-slate-900 leading-tight">Pollo a la brasa 1/4</h3>
                                <p class="mt-1 line-clamp-2 text-[11px] text-slate-500">Con papas fritas y ensalada.</p>
                                <p class="mt-auto pt-2 text-base font-black text-slate-900">S/ 18.00</p>
                            </div>
                        </article>

                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-amber-300 hover:shadow-md">
                            <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-red-100 to-rose-50">
                                <i class="fas fa-fish text-4xl text-red-500/40"></i>
                            </div>
                            <div class="flex flex-1 flex-col p-3">
                                <h3 class="text-sm font-bold text-slate-900 leading-tight">Ceviche clásico</h3>
                                <p class="mt-1 line-clamp-2 text-[11px] text-slate-500">Pescado del día, limón y camote.</p>
                                <p class="mt-auto pt-2 text-base font-black text-slate-900">S/ 22.00</p>
                            </div>
                        </article>

                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-amber-300 hover:shadow-md">
                            <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-emerald-100 to-teal-50">
                                <i class="fas fa-leaf text-4xl text-emerald-600/40"></i>
                            </div>
                            <div class="flex flex-1 flex-col p-3">
                                <h3 class="text-sm font-bold text-slate-900 leading-tight">Ensalada César</h3>
                                <p class="mt-1 line-clamp-2 text-[11px] text-slate-500">Lechuga, pollo, parmesano, aderezo.</p>
                                <p class="mt-auto pt-2 text-base font-black text-slate-900">S/ 14.50</p>
                            </div>
                        </article>

                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-amber-300 hover:shadow-md">
                            <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-sky-100 to-blue-50">
                                <i class="fas fa-glass-whiskey text-4xl text-sky-600/40"></i>
                            </div>
                            <div class="flex flex-1 flex-col p-3">
                                <h3 class="text-sm font-bold text-slate-900 leading-tight">Chicha morada 500 ml</h3>
                                <p class="mt-1 line-clamp-2 text-[11px] text-slate-500">Bebida tradicional.</p>
                                <p class="mt-auto pt-2 text-base font-black text-slate-900">S/ 5.00</p>
                            </div>
                        </article>

                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-amber-300 hover:shadow-md">
                            <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-violet-100 to-purple-50">
                                <i class="fas fa-ice-cream text-4xl text-violet-500/40"></i>
                            </div>
                            <div class="flex flex-1 flex-col p-3">
                                <h3 class="text-sm font-bold text-slate-900 leading-tight">Suspiro limeño</h3>
                                <p class="mt-1 line-clamp-2 text-[11px] text-slate-500">Postre clásico porción individual.</p>
                                <p class="mt-auto pt-2 text-base font-black text-slate-900">S/ 9.00</p>
                            </div>
                        </article>

                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-amber-300 hover:shadow-md">
                            <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-amber-50 to-yellow-100">
                                <i class="fas fa-bread-slice text-4xl text-amber-700/35"></i>
                            </div>
                            <div class="flex flex-1 flex-col p-3">
                                <h3 class="text-sm font-bold text-slate-900 leading-tight">Pan al ajo</h3>
                                <p class="mt-1 line-clamp-2 text-[11px] text-slate-500">Entrada para compartir.</p>
                                <p class="mt-auto pt-2 text-base font-black text-slate-900">S/ 8.00</p>
                            </div>
                        </article>

                    </div>
                </div>
            </section>

            <!-- Panel cuenta / comanda -->
            <aside class="flex w-full flex-col border-t border-slate-200 bg-white lg:w-[380px] lg:flex-shrink-0 lg:border-l lg:border-t-0 xl:w-[420px]">
                <div class="flex-shrink-0 border-b border-slate-100 px-4 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Comanda</p>
                            <p class="text-sm font-black text-slate-900">#0001 — Mesa 4</p>
                        </div>
                        <span class="rounded-lg bg-amber-100 px-2 py-1 text-[10px] font-bold uppercase text-amber-800">Abierta</span>
                    </div>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto px-4 py-3">
                    <ul class="space-y-3">
                        <li class="flex gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-white text-sm font-black text-slate-800 shadow-sm">2</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-slate-900">Pollo a la brasa 1/4</p>
                                <p class="text-xs text-slate-500">S/ 18.00 c/u</p>
                            </div>
                            <p class="flex-shrink-0 text-sm font-black text-slate-900">S/ 36.00</p>
                        </li>
                        <li class="flex gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-white text-sm font-black text-slate-800 shadow-sm">1</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-slate-900">Chicha morada 500 ml</p>
                                <p class="text-xs text-slate-500">S/ 5.00 c/u</p>
                            </div>
                            <p class="flex-shrink-0 text-sm font-black text-slate-900">S/ 5.00</p>
                        </li>
                        <li class="flex gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-white text-sm font-black text-slate-800 shadow-sm">1</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-slate-900">Ceviche clásico</p>
                                <p class="text-xs text-slate-500">S/ 22.00 c/u</p>
                            </div>
                            <p class="flex-shrink-0 text-sm font-black text-slate-900">S/ 22.00</p>
                        </li>
                    </ul>
                </div>

                <div class="flex-shrink-0 border-t border-slate-100 bg-slate-50 px-4 py-4">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-800">S/ 63.00</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Servicio (10%)</span>
                            <span class="font-semibold text-slate-800">S/ 6.30</span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-black text-slate-900">
                            <span>Total</span>
                            <span>S/ 69.30</span>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <button type="button" class="rounded-xl border border-slate-200 bg-white py-3 text-xs font-bold text-slate-700 shadow-sm">
                            <i class="fas fa-print mr-1 text-slate-400"></i> Precuenta
                        </button>
                        <button type="button" class="rounded-xl border border-slate-200 bg-white py-3 text-xs font-bold text-slate-700 shadow-sm">
                            <i class="fas fa-fire mr-1 text-orange-500"></i> Enviar cocina
                        </button>
                        <button type="button" class="col-span-2 rounded-xl bg-emerald-600 py-3.5 text-sm font-black text-white shadow-lg shadow-emerald-600/25">
                            <i class="fas fa-check-circle mr-2"></i> Cobrar
                        </button>
                        <button type="button" class="col-span-2 rounded-xl border-2 border-dashed border-slate-300 bg-transparent py-2.5 text-xs font-bold text-slate-500">
                            Nueva comanda
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
