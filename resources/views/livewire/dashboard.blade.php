<div>

{{-- ═══════════════════════════════════════════════════════════
     TAB 1 — CARGAR MOVIMIENTO
═══════════════════════════════════════════════════════════ --}}
@if ($activeTab === 'cargar')
<div class="space-y-4">

    {{-- Confirmación guardado --}}
    @if ($savedMsg)
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2500)"
         x-transition:leave="transition duration-500" x-transition:leave-end="opacity-0 -translate-y-2"
         class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ $savedMsg }}
    </div>
    @endif

    {{-- Banner modo edición --}}
    @if ($editingMovement)
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-amber-700">
            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Editando movimiento</span>
        </div>
        <button wire:click="resetForm" class="text-xs text-amber-600 hover:text-amber-800 underline">
            Cancelar
        </button>
    </div>
    @endif

    {{-- Tipo --}}
    <div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tipo</p>
        <div class="grid grid-cols-2 gap-3">
            <button type="button" wire:click="$set('type','gasto')"
                    class="py-3 rounded-xl text-sm font-semibold border-2 transition-all
                    {{ $type === 'gasto'
                        ? 'bg-red-50 border-red-400 text-red-700 shadow-sm'
                        : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300' }}">
                <span class="text-lg block mb-0.5">💸</span>
                Gasto
            </button>
            <button type="button" wire:click="$set('type','ingreso')"
                    class="py-3 rounded-xl text-sm font-semibold border-2 transition-all
                    {{ $type === 'ingreso'
                        ? 'bg-green-50 border-green-400 text-green-700 shadow-sm'
                        : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300' }}">
                <span class="text-lg block mb-0.5">💰</span>
                Ingreso
            </button>
        </div>
    </div>

    {{-- Formulario --}}
    <form wire:submit="save" class="space-y-4">

        {{-- Monto grande --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <label class="block text-xs font-medium text-gray-500 mb-1">Monto *</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-2xl font-light text-gray-400">$</span>
                <input wire:model="amount" type="number" step="0.01" min="0" placeholder="0,00"
                       autofocus
                       class="w-full pl-8 pr-3 py-2 text-3xl font-bold text-gray-900 border-0 outline-none bg-transparent
                              @error('amount') text-red-500 @enderror">
            </div>
            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Categoría + Persona --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
                <label class="block text-xs font-medium text-gray-500 mb-1">Categoría *</label>
                <select wire:model="category"
                        class="w-full text-sm font-medium text-gray-800 border-0 outline-none bg-transparent">
                    @foreach ($categoriasActual as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
                <label class="block text-xs font-medium text-gray-500 mb-1">¿Quién? *</label>
                <select wire:model="person"
                        class="w-full text-sm font-medium text-gray-800 border-0 outline-none bg-transparent">
                    <option value="persona_a">{{ $personaA }}</option>
                    <option value="persona_b">{{ $personaB }}</option>
                </select>
            </div>
        </div>

        {{-- Fecha + Descripción --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
                <label class="block text-xs font-medium text-gray-500 mb-1">Fecha *</label>
                <input wire:model="date" type="date"
                       class="w-full text-sm font-medium text-gray-800 border-0 outline-none bg-transparent
                              @error('date') text-red-500 @enderror">
                @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
                <label class="block text-xs font-medium text-gray-500 mb-1">Descripción</label>
                <input wire:model="description" type="text" placeholder="opcional…"
                       class="w-full text-sm font-medium text-gray-800 border-0 outline-none bg-transparent placeholder-gray-300">
            </div>
        </div>

        {{-- Info 50/50 para gastos --}}
        @if ($type === 'gasto' && $amount > 0)
        <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-2.5 text-xs text-indigo-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Cada uno aporta <strong class="mx-1">${{ number_format((float)$amount / 2, 2) }}</strong>
            (regla 50/50)
        </div>
        @endif

        {{-- Botón guardar --}}
        <button type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3.5 rounded-xl font-semibold text-white text-sm transition-all
                       flex items-center justify-center gap-2 disabled:opacity-60
                       {{ $editingMovement ? 'bg-amber-500 hover:bg-amber-600' : ($type === 'gasto' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600') }}">
            <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="{{ $editingMovement ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M5 13l4 4L19 7' }}"/>
            </svg>
            {{ $editingMovement ? 'Guardar cambios' : 'Guardar' }}
        </button>
    </form>
</div>
@endif


{{-- ═══════════════════════════════════════════════════════════
     TAB 2 — BALANCE Y LISTA
═══════════════════════════════════════════════════════════ --}}
@if ($activeTab === 'balance')
<div class="space-y-4">

    {{-- Filtros --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm space-y-3">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Filtros</p>

        {{-- Período --}}
        <div class="grid grid-cols-3 gap-2">
            <div>
                <label class="block text-xs text-gray-400 mb-1">Período</label>
                <select wire:model.live="filterPeriodType"
                        class="w-full text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white">
                    <option value="month">Mes</option>
                    <option value="year">Año</option>
                </select>
            </div>
            @if ($filterPeriodType === 'month')
            <div>
                <label class="block text-xs text-gray-400 mb-1">Mes</label>
                <select wire:model.live="filterMonth"
                        class="w-full text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white">
                    @foreach ($meses as $num => $nombre)
                        <option value="{{ $num }}">{{ substr($nombre, 0, 3) }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="block text-xs text-gray-400 mb-1">Año</label>
                <select wire:model.live="filterYear"
                        class="w-full text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white">
                    @for ($y = now()->year + 1; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- Persona + Categoría --}}
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs text-gray-400 mb-1">Persona</label>
                <select wire:model.live="filterPerson"
                        class="w-full text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white">
                    <option value="">Todos</option>
                    <option value="persona_a">{{ $personaA }}</option>
                    <option value="persona_b">{{ $personaB }}</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Categoría</label>
                <select wire:model.live="filterCategory"
                        class="w-full text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white">
                    <option value="">Todas</option>
                    @foreach ($todasCategorias as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Tarjetas de Balance --}}
    <div class="grid grid-cols-2 gap-3">
        {{-- Total gastos --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-gray-400">Total gastos</p>
            <p class="text-xl font-bold text-gray-900 mt-0.5">${{ number_format($balance['totalGastos'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $periodLabel }}</p>
        </div>

        {{-- Balance --}}
        <div class="rounded-xl border p-4 shadow-sm
            {{ $balance['equilibrado'] ? 'bg-green-50 border-green-200' : 'bg-amber-50 border-amber-200' }}">
            <p class="text-xs {{ $balance['equilibrado'] ? 'text-green-500' : 'text-amber-500' }}">Balance</p>
            @if ($balance['equilibrado'])
                <p class="text-xl font-bold text-green-700 mt-0.5">Equilibrado ✓</p>
                <p class="text-xs text-green-500 mt-1">Cada uno pagó lo mismo</p>
            @else
                <p class="text-xl font-bold text-amber-700 mt-0.5">${{ number_format($balance['diferencia'], 2) }}</p>
                <p class="text-xs text-amber-600 mt-1">
                    <strong>{{ $balance['deudor'] === 'persona_a' ? $personaA : $personaB }}</strong>
                    le debe a
                    <strong>{{ $balance['acreedor'] === 'persona_a' ? $personaA : $personaB }}</strong>
                </p>
            @endif
        </div>

        {{-- Persona A --}}
        <div class="bg-white border border-blue-100 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-blue-500 font-medium">{{ $personaA }}</p>
            <p class="text-xl font-bold text-gray-900 mt-0.5">${{ number_format($balance['gastosA'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Ingresos: <span class="text-green-600">${{ number_format($ingresosA, 2) }}</span></p>
        </div>

        {{-- Persona B --}}
        <div class="bg-white border border-pink-100 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-pink-500 font-medium">{{ $personaB }}</p>
            <p class="text-xl font-bold text-gray-900 mt-0.5">${{ number_format($balance['gastosB'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Ingresos: <span class="text-green-600">${{ number_format($ingresosB, 2) }}</span></p>
        </div>
    </div>

    {{-- Botón exportar Excel --}}
    <button wire:click="downloadExcel" wire:loading.attr="disabled"
            class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700
                   disabled:opacity-60 text-white text-sm font-semibold py-3 rounded-xl transition-colors shadow-sm">
        <svg wire:loading wire:target="downloadExcel" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <svg wire:loading.remove wire:target="downloadExcel" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span wire:loading.remove wire:target="downloadExcel">Exportar a Excel · {{ $movements->count() }} registros</span>
        <span wire:loading wire:target="downloadExcel">Generando archivo…</span>
    </button>

    {{-- Lista de movimientos --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-700">Movimientos</span>
            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $movements->count() }}</span>
        </div>

        @if ($movements->isEmpty())
        <div class="py-10 text-center">
            <p class="text-sm text-gray-400">Sin movimientos en este período.</p>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach ($movements as $movement)
            <div class="flex items-center gap-3 px-4 py-3 group">

                {{-- Ícono --}}
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0
                    {{ $movement->type === 'gasto' ? 'bg-red-50' : 'bg-green-50' }}">
                    @if ($movement->type === 'gasto')
                    <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                    @else
                    <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-xs font-medium px-1.5 py-0.5 rounded
                            {{ $movement->person === 'persona_a' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-600' }}">
                            {{ $movement->person === 'persona_a' ? $personaA : $personaB }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $movement->category }}</span>
                        @if ($movement->description)
                        <span class="text-xs text-gray-400 truncate">· {{ $movement->description }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $movement->date->format('d/m/Y') }}
                        @if ($movement->type === 'gasto')
                        <span class="text-gray-300">· c/u ${{ number_format($movement->amount / 2, 2) }}</span>
                        @endif
                    </p>
                </div>

                {{-- Monto --}}
                <div class="text-right shrink-0">
                    <span class="text-sm font-bold {{ $movement->type === 'gasto' ? 'text-red-500' : 'text-green-500' }}">
                        {{ $movement->type === 'gasto' ? '−' : '+' }}${{ number_format($movement->amount, 2) }}
                    </span>
                </div>

                {{-- Acciones --}}
                @if ($confirmDelete === $movement->id)
                <div class="flex gap-1 shrink-0">
                    <button wire:click="delete"
                            class="text-xs bg-red-500 text-white px-2 py-1 rounded-lg">Borrar</button>
                    <button wire:click="cancelDelete"
                            class="text-xs text-gray-400 px-2 py-1 rounded-lg hover:bg-gray-100">No</button>
                </div>
                @else
                <div class="flex items-center gap-0.5 shrink-0">
                    <button wire:click="editMovement({{ $movement->id }})"
                            class="text-gray-300 hover:text-amber-500 transition-colors p-1"
                            title="Editar">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button wire:click="askDelete({{ $movement->id }})"
                            class="text-gray-300 hover:text-red-400 transition-colors p-1"
                            title="Eliminar">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Totales footer --}}
        <div class="border-t border-gray-100 px-4 py-2.5 bg-gray-50">
            <div class="flex items-center justify-between text-xs text-gray-500">
                <div class="flex gap-3">
                    <span>Gastos: <strong class="text-red-500">${{ number_format($balance['totalGastos'], 2) }}</strong></span>
                    <span>Ingresos: <strong class="text-green-500">${{ number_format($totalIngresos, 2) }}</strong></span>
                </div>
                <span>Neto:
                    <strong class="{{ ($totalIngresos - $balance['totalGastos']) >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        ${{ number_format($totalIngresos - $balance['totalGastos'], 2) }}
                    </strong>
                </span>
            </div>
        </div>
        @endif
    </div>

</div>
@endif


{{-- ═══════════════════════════════════════════════════════════
     TAB 3 — ESTADÍSTICAS + IA
═══════════════════════════════════════════════════════════ --}}
@if ($activeTab === 'estadisticas')
<div class="space-y-4">

    {{-- Filtro período (comparte estado con balance) --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
        <div class="flex items-center gap-2">
            <select wire:model.live="filterPeriodType"
                    class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white">
                <option value="month">Mes</option>
                <option value="year">Año</option>
            </select>
            @if ($filterPeriodType === 'month')
            <select wire:model.live="filterMonth"
                    class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white">
                @foreach ($meses as $num => $nombre)
                    <option value="{{ $num }}">{{ $nombre }}</option>
                @endforeach
            </select>
            @endif
            <select wire:model.live="filterYear"
                    class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white">
                @for ($y = now()->year + 1; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
            <span class="text-xs text-gray-400 ml-auto font-medium">{{ $periodLabel }}</span>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm text-center">
            <p class="text-xs text-gray-400">Gastos</p>
            <p class="text-lg font-bold text-red-500 mt-0.5">${{ number_format($stats['totalGastos'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-300">{{ $stats['cantGastos'] }} registros</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm text-center">
            <p class="text-xs text-gray-400">Ingresos</p>
            <p class="text-lg font-bold text-green-500 mt-0.5">${{ number_format($stats['totalIngresos'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-300">{{ $stats['cantIngresos'] }} registros</p>
        </div>
        <div class="rounded-xl border p-3 shadow-sm text-center
            {{ $stats['ahorro'] >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }}">
            <p class="text-xs {{ $stats['ahorro'] >= 0 ? 'text-emerald-500' : 'text-red-400' }}">Ahorro</p>
            <p class="text-lg font-bold {{ $stats['ahorro'] >= 0 ? 'text-emerald-700' : 'text-red-600' }} mt-0.5">
                ${{ number_format(abs($stats['ahorro']), 0, ',', '.') }}
            </p>
            <p class="text-xs {{ $stats['ahorro'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                {{ $stats['tasaAhorro'] }}%
            </p>
        </div>
    </div>


    {{-- Gastos por categoría --}}
    @if (count($stats['porCategoria']) > 0)
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Gastos por categoría</p>
        <div class="space-y-2.5">
            @foreach ($stats['porCategoria'] as $item)
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-gray-700">{{ $item['categoria'] }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">{{ $item['porcentaje'] }}%</span>
                        <span class="text-xs font-semibold text-gray-800">${{ number_format($item['monto'], 2) }}</span>
                    </div>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500
                        @php
                            $colors = ['bg-red-400','bg-orange-400','bg-amber-400','bg-yellow-400','bg-lime-400','bg-green-400','bg-teal-400','bg-cyan-400','bg-sky-400','bg-indigo-400'];
                            echo $colors[$loop->index % count($colors)];
                        @endphp"
                         style="width: {{ $item['porcentaje'] }}%">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Comparativa A vs B --}}
    @if ($balance['totalGastos'] > 0)
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Aporte en gastos</p>
        <div class="space-y-3">
            {{-- Persona A --}}
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-xs font-medium text-blue-600">{{ $personaA }}</span>
                    <span class="text-xs font-semibold text-gray-700">${{ number_format($balance['gastosA'], 2) }}
                        @if ($balance['totalGastos'] > 0)
                        <span class="text-gray-400 font-normal">({{ round($balance['gastosA'] / $balance['totalGastos'] * 100) }}%)</span>
                        @endif
                    </span>
                </div>
                <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full transition-all duration-500"
                         style="width: {{ $balance['totalGastos'] > 0 ? round($balance['gastosA'] / $balance['totalGastos'] * 100) : 0 }}%">
                    </div>
                </div>
            </div>
            {{-- Persona B --}}
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-xs font-medium text-pink-500">{{ $personaB }}</span>
                    <span class="text-xs font-semibold text-gray-700">${{ number_format($balance['gastosB'], 2) }}
                        @if ($balance['totalGastos'] > 0)
                        <span class="text-gray-400 font-normal">({{ round($balance['gastosB'] / $balance['totalGastos'] * 100) }}%)</span>
                        @endif
                    </span>
                </div>
                <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-pink-400 rounded-full transition-all duration-500"
                         style="width: {{ $balance['totalGastos'] > 0 ? round($balance['gastosB'] / $balance['totalGastos'] * 100) : 0 }}%">
                    </div>
                </div>
            </div>
            {{-- Línea 50% --}}
            <p class="text-xs text-gray-400 text-center">Objetivo: 50% cada uno</p>
        </div>
    </div>
    @endif

    {{-- Evolución mensual (solo vista anual) --}}
    @if ($filterPeriodType === 'year' && count($stats['porMes']) > 0)
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Evolución mensual {{ $filterYear }}</p>
        <div class="flex items-end gap-1 h-24">
            @foreach ($stats['porMes'] as $mes)
            <div class="flex-1 flex flex-col items-center gap-0.5">
                {{-- Barras --}}
                <div class="w-full flex gap-0.5 items-end" style="height: 72px">
                    <div class="flex-1 bg-red-300 rounded-t transition-all duration-500"
                         style="height: {{ $mes['pctGastos'] }}%; min-height: {{ $mes['gastos'] > 0 ? '4px' : '0' }}">
                    </div>
                    <div class="flex-1 bg-green-300 rounded-t transition-all duration-500"
                         style="height: {{ $mes['pctIngresos'] }}%; min-height: {{ $mes['ingresos'] > 0 ? '4px' : '0' }}">
                    </div>
                </div>
                <span class="text-[9px] text-gray-400">{{ $mes['label'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="flex items-center gap-3 mt-2 justify-center">
            <span class="flex items-center gap-1 text-xs text-gray-400">
                <span class="w-2 h-2 bg-red-300 rounded-sm inline-block"></span> Gastos
            </span>
            <span class="flex items-center gap-1 text-xs text-gray-400">
                <span class="w-2 h-2 bg-green-300 rounded-sm inline-block"></span> Ingresos
            </span>
        </div>
    </div>
    @endif


</div>
@endif


{{-- ═══════════════════════════════════════════════════════════
     TAB 4 — CONFIGURACIÓN (Personas + Categorías)
═══════════════════════════════════════════════════════════ --}}
@if ($activeTab === 'config')
<div class="space-y-4">

    {{-- Selector de sección --}}
    <div class="grid grid-cols-2 gap-2 bg-gray-100 p-1 rounded-xl">
        <button wire:click="$set('configSection','personas')"
                class="py-2 rounded-lg text-sm font-semibold transition-all
                {{ $configSection === 'personas'
                    ? 'bg-white text-gray-800 shadow-sm'
                    : 'text-gray-400 hover:text-gray-600' }}">
            Personas
        </button>
        <button wire:click="$set('configSection','categorias')"
                class="py-2 rounded-lg text-sm font-semibold transition-all
                {{ $configSection === 'categorias'
                    ? 'bg-white text-gray-800 shadow-sm'
                    : 'text-gray-400 hover:text-gray-600' }}">
            Categorías
        </button>
    </div>

    {{-- ── PERSONAS ─────────────────────────────────────────── --}}
    @if ($configSection === 'personas')
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm space-y-4">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombres de las personas</p>

        @if ($personasSaved)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2500)"
             class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-3 py-2">
            {{ $personasSaved }}
        </div>
        @endif

        <div class="space-y-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Persona A</label>
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-xs font-bold text-blue-600 shrink-0">A</span>
                    <input wire:model="editPersonaA" type="text" maxlength="50"
                           placeholder="Nombre de la persona A"
                           class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                  @error('editPersonaA') border-red-400 @enderror">
                </div>
                @error('editPersonaA') <p class="text-red-500 text-xs mt-1 ml-9">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Persona B</label>
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 bg-pink-100 rounded-full flex items-center justify-center text-xs font-bold text-pink-600 shrink-0">B</span>
                    <input wire:model="editPersonaB" type="text" maxlength="50"
                           placeholder="Nombre de la persona B"
                           class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                  @error('editPersonaB') border-red-400 @enderror">
                </div>
                @error('editPersonaB') <p class="text-red-500 text-xs mt-1 ml-9">{{ $message }}</p> @enderror
            </div>
        </div>

        <button wire:click="savePersonas"
                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm
                       font-semibold rounded-xl transition-colors">
            Guardar nombres
        </button>

        <p class="text-xs text-gray-400 text-center">
            Los nombres se usan en el balance, los listados y las exportaciones.
        </p>
    </div>
    @endif

    {{-- ── CATEGORÍAS ───────────────────────────────────────── --}}
    @if ($configSection === 'categorias')
    <div class="space-y-3">

        {{-- Toggle Gastos / Ingresos --}}
        <div class="grid grid-cols-2 gap-2">
            <button wire:click="$set('configCatType','gasto')"
                    class="py-2 rounded-xl text-sm font-semibold border-2 transition-all
                    {{ $configCatType === 'gasto'
                        ? 'bg-red-50 border-red-400 text-red-700'
                        : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300' }}">
                Gastos
            </button>
            <button wire:click="$set('configCatType','ingreso')"
                    class="py-2 rounded-xl text-sm font-semibold border-2 transition-all
                    {{ $configCatType === 'ingreso'
                        ? 'bg-green-50 border-green-400 text-green-700'
                        : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300' }}">
                Ingresos
            </button>
        </div>

        {{-- Lista de categorías --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            @if ($configCats->isEmpty())
            <div class="py-8 text-center text-sm text-gray-400">
                No hay categorías. Agregá una abajo.
            </div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach ($configCats as $cat)
                <div class="flex items-center gap-3 px-4 py-3">

                    @if ($editingCatId === $cat->id)
                    {{-- Modo edición inline --}}
                    <div class="flex-1 flex items-center gap-2">
                        <input wire:model="editingCatName" type="text" maxlength="50"
                               wire:keydown.enter="saveEditCat"
                               wire:keydown.escape="cancelEditCat"
                               autofocus
                               class="flex-1 text-sm border border-indigo-300 rounded-lg px-2 py-1.5
                                      focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                      @error('editingCatName') border-red-400 @enderror">
                        <button wire:click="saveEditCat"
                                class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition-colors">
                            Guardar
                        </button>
                        <button wire:click="cancelEditCat"
                                class="text-xs text-gray-400 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                            Cancelar
                        </button>
                    </div>
                    @error('editingCatName')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror

                    @elseif ($confirmDeleteCat === $cat->id)
                    {{-- Confirmación eliminar --}}
                    <span class="flex-1 text-sm text-gray-500 line-through">{{ $cat->name }}</span>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs text-red-500">¿Eliminar?</span>
                        <button wire:click="deleteCat"
                                class="text-xs bg-red-500 text-white px-2 py-1 rounded-lg hover:bg-red-600 transition-colors">
                            Sí
                        </button>
                        <button wire:click="cancelDeleteCat"
                                class="text-xs text-gray-400 px-2 py-1 rounded-lg hover:bg-gray-100 transition-colors">
                            No
                        </button>
                    </div>

                    @else
                    {{-- Vista normal --}}
                    <div class="w-2 h-2 rounded-full shrink-0
                        {{ $configCatType === 'gasto' ? 'bg-red-300' : 'bg-green-300' }}">
                    </div>
                    <span class="flex-1 text-sm text-gray-700">{{ $cat->name }}</span>
                    <div class="flex items-center gap-1">
                        <button wire:click="startEditCat({{ $cat->id }})"
                                class="text-xs text-gray-400 hover:text-indigo-600 px-2 py-1
                                       rounded-lg hover:bg-indigo-50 transition-colors">
                            Editar
                        </button>
                        <button wire:click="askDeleteCat({{ $cat->id }})"
                                class="text-xs text-gray-400 hover:text-red-500 px-2 py-1
                                       rounded-lg hover:bg-red-50 transition-colors">
                            Eliminar
                        </button>
                    </div>
                    @endif

                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Agregar nueva categoría --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                Agregar categoría de {{ $configCatType === 'gasto' ? 'gastos' : 'ingresos' }}
            </p>
            <div class="flex gap-2">
                <input wire:model="newCatName" type="text" maxlength="50"
                       wire:keydown.enter="addCategory"
                       placeholder="Nombre de la categoría…"
                       class="flex-1 text-sm border border-gray-200 rounded-xl px-3 py-2.5
                              focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                              @error('newCatName') border-red-400 @enderror">
                <button wire:click="addCategory"
                        class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white text-sm
                               font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    Agregar
                </button>
            </div>
            @error('newCatName') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

    </div>
    @endif

</div>
@endif


{{-- ═══════════════════════════════════════════════════════════
     TAB 4 — DATOS (EXPORT / IMPORT)
═══════════════════════════════════════════════════════════ --}}
@if ($activeTab === 'datos')
<div class="space-y-4">

    {{-- Exportar Excel (período actual) --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm space-y-2">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Exportar a Excel</p>
        <p class="text-xs text-gray-400">
            Descarga un archivo <strong>.csv</strong> del período <strong>{{ $periodLabel }}</strong>
            con balance y detalle. Se abre directo en Excel para imprimir.
        </p>
        <button wire:click="downloadExcel" wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700
                       disabled:opacity-60 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
            <svg wire:loading wire:target="downloadExcel" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <svg wire:loading.remove wire:target="downloadExcel" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span wire:loading.remove wire:target="downloadExcel">Descargar Excel · {{ $periodLabel }}</span>
            <span wire:loading wire:target="downloadExcel">Generando…</span>
        </button>
    </div>

    {{-- Backup JSON --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm space-y-2">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Backup completo</p>
        <p class="text-xs text-gray-400">
            Exporta <strong>todos los {{ $totalRegistros }} registros</strong> en formato JSON.
            Guardalo en Google Drive o tu computadora para no perder nada.
        </p>
        <button wire:click="downloadBackup" wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700
                       disabled:opacity-60 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
            <svg wire:loading wire:target="downloadBackup" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <svg wire:loading.remove wire:target="downloadBackup" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
            </svg>
            <span wire:loading.remove wire:target="downloadBackup">Descargar Backup JSON</span>
            <span wire:loading wire:target="downloadBackup">Preparando…</span>
        </button>
    </div>

    {{-- Importar backup --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm space-y-3">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Restaurar desde Backup</p>
        <p class="text-xs text-gray-400">Seleccioná un archivo JSON exportado previamente desde esta app.</p>

        {{-- Mensaje de resultado --}}
        @if ($importMessage)
        <div class="px-3 py-2 rounded-xl text-sm font-medium
            {{ $importStatus === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' }}">
            {{ $importMessage }}
        </div>
        @endif

        {{-- Opción reemplazar --}}
        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
            <input type="checkbox" wire:model="importReplace"
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span>Reemplazar todos los datos existentes</span>
        </label>
        @if ($importReplace)
        <div class="bg-red-50 border border-red-200 rounded-xl px-3 py-2 text-xs text-red-700">
            ⚠️ Se borrarán todos los registros actuales antes de importar.
        </div>
        @endif

        {{-- Input de archivo con Alpine.js --}}
        <div x-data="{
            fileName: '',
            loading: false,
            async handleFile(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.fileName = file.name;
                this.loading = true;
                try {
                    const text = await file.text();
                    await $wire.set('importJson', text);
                    await $wire.call('importBackup');
                } finally {
                    this.loading = false;
                    this.fileName = '';
                    event.target.value = '';
                }
            }
        }">
            <label class="w-full flex items-center justify-center gap-2 border-2 border-dashed
                          border-gray-300 hover:border-indigo-400 rounded-xl py-6 cursor-pointer
                          transition-colors text-sm text-gray-500 hover:text-indigo-600"
                   :class="{ 'opacity-60 pointer-events-none': loading }">
                <input type="file" accept=".json" @change="handleFile($event)" class="hidden">
                <template x-if="!loading">
                    <div class="text-center">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span x-text="fileName || 'Tocá para seleccionar el archivo .json'"></span>
                    </div>
                </template>
                <template x-if="loading">
                    <div class="text-center">
                        <svg class="w-6 h-6 animate-spin text-indigo-500 mx-auto mb-1" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span>Importando…</span>
                    </div>
                </template>
            </label>
        </div>
    </div>


</div>
@endif


{{-- ═══════════════════════════════════════════════════════════
     ZÓCALO — Bottom Navigation Bar
═══════════════════════════════════════════════════════════ --}}
<div class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200"
     style="padding-bottom: env(safe-area-inset-bottom);">
    <div class="max-w-2xl mx-auto flex">

        {{-- Cargar --}}
        <button wire:click="$set('activeTab','cargar')"
                class="flex-1 flex flex-col items-center py-3 gap-0.5 transition-colors
                {{ $activeTab === 'cargar' ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="text-[10px] font-medium">Cargar</span>
            @if ($activeTab === 'cargar')
            <span class="w-1 h-1 bg-indigo-600 rounded-full"></span>
            @endif
        </button>

        {{-- Balance --}}
        <button wire:click="$set('activeTab','balance')"
                class="flex-1 flex flex-col items-center py-3 gap-0.5 transition-colors
                {{ $activeTab === 'balance' ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
            </svg>
            <span class="text-[10px] font-medium">Balance</span>
            @if ($activeTab === 'balance')
            <span class="w-1 h-1 bg-indigo-600 rounded-full"></span>
            @endif
        </button>

        {{-- Estadísticas --}}
        <button wire:click="$set('activeTab','estadisticas')"
                class="flex-1 flex flex-col items-center py-3 gap-0.5 transition-colors
                {{ $activeTab === 'estadisticas' ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span class="text-[9px] font-medium">Estadísticas</span>
            @if ($activeTab === 'estadisticas')
            <span class="w-1 h-1 bg-indigo-600 rounded-full"></span>
            @endif
        </button>

        {{-- Config --}}
        <button wire:click="$set('activeTab','config')"
                class="flex-1 flex flex-col items-center py-3 gap-0.5 transition-colors
                {{ $activeTab === 'config' ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="text-[9px] font-medium">Configuración</span>
            @if ($activeTab === 'config')
            <span class="w-1 h-1 bg-indigo-600 rounded-full"></span>
            @endif
        </button>

        {{-- Datos --}}
        <button wire:click="$set('activeTab','datos')"
                class="flex-1 flex flex-col items-center py-3 gap-0.5 transition-colors
                {{ $activeTab === 'datos' ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
            </svg>
            <span class="text-[10px] font-medium">Datos</span>
            @if ($activeTab === 'datos')
            <span class="w-1 h-1 bg-indigo-600 rounded-full"></span>
            @endif
        </button>

    </div>
</div>

</div>
