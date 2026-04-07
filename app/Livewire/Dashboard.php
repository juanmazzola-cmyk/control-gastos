<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Movement;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    // ─── Navegación ────────────────────────────────────────────────
    public string $activeTab = 'cargar';

    // ─── Formulario ────────────────────────────────────────────────
    public string $amount      = '';
    public string $type        = 'gasto';
    public string $category    = '';
    public string $description = '';
    public string $date        = '';
    public string $person      = 'persona_a';

    // ─── Filtros ───────────────────────────────────────────────────
    public string $filterPeriodType = 'month';
    public int    $filterYear;
    public int    $filterMonth;
    public string $filterPerson   = '';
    public string $filterCategory = '';

    // ─── UI ────────────────────────────────────────────────────────
    public ?int   $confirmDelete    = null;
    public string $savedMsg         = '';
    public ?int   $editingMovement  = null;  // ID del movimiento en edición

    // ─── Config — Personas ─────────────────────────────────────────
    public string $configSection  = 'personas';
    public string $editPersonaA   = '';
    public string $editPersonaB   = '';
    public string $personasSaved  = '';

    // ─── Config — Categorías ───────────────────────────────────────
    public string $configCatType    = 'gasto';
    public string $newCatName       = '';
    public ?int   $editingCatId     = null;
    public string $editingCatName   = '';
    public ?int   $confirmDeleteCat = null;

    // ─── Importar ──────────────────────────────────────────────────
    public string $importJson    = '';
    public string $importMessage = '';
    public string $importStatus  = '';
    public bool   $importReplace = false;

    // ─── Eliminar todo ─────────────────────────────────────────────
    public bool $confirmDeleteAll = false;

    // ──────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->filterYear  = (int) now()->format('Y');
        $this->filterMonth = (int) now()->format('m');
        $this->date        = now()->format('Y-m-d');
        $this->resetCategoryToFirst();
        $this->loadPersonaSettings();
    }

    private function loadPersonaSettings(): void
    {
        $this->editPersonaA = Setting::get('persona_a') ?? config('personas.persona_a', 'Persona A');
        $this->editPersonaB = Setting::get('persona_b') ?? config('personas.persona_b', 'Persona B');
    }

    private function resetCategoryToFirst(): void
    {
        $first = Category::where('type', $this->type)->ordenadas()->value('name');
        $this->category = $first ?? '';
    }

    private function getPersonaName(string $key): string
    {
        return Setting::get($key) ?? config('personas.' . $key, ucfirst(str_replace('_', ' ', $key)));
    }

    // ─── FORMULARIO ────────────────────────────────────────────────
    public function updatedType(): void
    {
        $this->resetCategoryToFirst();
    }

    public function save(): void
    {
        $this->validate([
            'amount'      => 'required|numeric|min:0.01|max:999999999',
            'type'        => 'required|in:gasto,ingreso',
            'category'    => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'date'        => 'required|date',
            'person'      => 'required|in:persona_a,persona_b',
        ], [
            'amount.required'   => 'El monto es obligatorio.',
            'amount.numeric'    => 'El monto debe ser un número.',
            'amount.min'        => 'El monto debe ser mayor a cero.',
            'date.required'     => 'La fecha es obligatoria.',
            'date.date'         => 'La fecha no es válida.',
            'category.required' => 'La categoría es obligatoria.',
        ]);

        $data = [
            'amount'      => $this->amount,
            'type'        => $this->type,
            'category'    => $this->category,
            'description' => $this->description ?: null,
            'date'        => $this->date,
            'person'      => $this->person,
        ];

        if ($this->editingMovement) {
            Movement::findOrFail($this->editingMovement)->update($data);
            $this->savedMsg = 'Movimiento actualizado.';
        } else {
            Movement::create($data);
            $this->savedMsg = ($this->type === 'gasto' ? 'Gasto' : 'Ingreso') . ' guardado.';
        }

        $this->resetForm();
    }

    public function editMovement(int $id): void
    {
        $m = Movement::findOrFail($id);

        $this->editingMovement = $id;
        $this->amount          = (string) $m->amount;
        $this->type            = $m->type;
        $this->category        = $m->category;
        $this->description     = $m->description ?? '';
        $this->date            = $m->date->format('Y-m-d');
        $this->person          = $m->person;
        $this->confirmDelete   = null;
        $this->activeTab       = 'cargar';
    }

    public function resetForm(): void
    {
        $this->editingMovement = null;
        $this->amount          = '';
        $this->description     = '';
        $this->date            = now()->format('Y-m-d');
        $this->type            = 'gasto';
        $this->resetCategoryToFirst();
        $this->person          = 'persona_a';
    }

    // ─── ELIMINAR MOVIMIENTO ───────────────────────────────────────
    public function askDelete(int $id): void  { $this->confirmDelete = $id; }
    public function cancelDelete(): void       { $this->confirmDelete = null; }

    public function delete(): void
    {
        if ($this->confirmDelete) {
            Movement::findOrFail($this->confirmDelete)->delete();
            $this->confirmDelete = null;
        }
    }

    // ─── CONFIG — PERSONAS ─────────────────────────────────────────
    public function savePersonas(): void
    {
        $this->validate([
            'editPersonaA' => 'required|string|max:50',
            'editPersonaB' => 'required|string|max:50',
        ], [
            'editPersonaA.required' => 'El nombre no puede estar vacío.',
            'editPersonaB.required' => 'El nombre no puede estar vacío.',
        ]);

        Setting::set('persona_a', trim($this->editPersonaA));
        Setting::set('persona_b', trim($this->editPersonaB));

        $this->personasSaved = 'Nombres guardados correctamente.';
    }

    // ─── CONFIG — CATEGORÍAS ───────────────────────────────────────
    public function addCategory(): void
    {
        $this->validate(
            ['newCatName' => 'required|string|max:50'],
            ['newCatName.required' => 'Escribí un nombre para la categoría.']
        );

        $name = trim($this->newCatName);

        $exists = Category::where('type', $this->configCatType)
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->exists();

        if ($exists) {
            $this->addError('newCatName', 'Ya existe una categoría con ese nombre.');
            return;
        }

        $maxPos = Category::where('type', $this->configCatType)->max('position') ?? 0;

        Category::create([
            'name'     => $name,
            'type'     => $this->configCatType,
            'position' => $maxPos + 1,
        ]);

        $this->newCatName = '';
    }

    public function startEditCat(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editingCatId   = $id;
        $this->editingCatName = $cat->name;
        $this->confirmDeleteCat = null;
    }

    public function cancelEditCat(): void
    {
        $this->editingCatId   = null;
        $this->editingCatName = '';
    }

    public function saveEditCat(): void
    {
        $this->validate(
            ['editingCatName' => 'required|string|max:50'],
            ['editingCatName.required' => 'El nombre no puede estar vacío.']
        );

        $name = trim($this->editingCatName);

        $exists = Category::where('type', $this->configCatType)
            ->where('id', '!=', $this->editingCatId)
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->exists();

        if ($exists) {
            $this->addError('editingCatName', 'Ya existe una categoría con ese nombre.');
            return;
        }

        Category::findOrFail($this->editingCatId)->update(['name' => $name]);

        $this->editingCatId   = null;
        $this->editingCatName = '';
    }

    public function askDeleteCat(int $id): void  { $this->confirmDeleteCat = $id; }
    public function cancelDeleteCat(): void       { $this->confirmDeleteCat = null; }

    public function deleteCat(): void
    {
        if ($this->confirmDeleteCat) {
            Category::findOrFail($this->confirmDeleteCat)->delete();
            $this->confirmDeleteCat = null;
            $this->editingCatId     = null;
        }
    }

    // ─── EXPORTAR EXCEL CSV ────────────────────────────────────────
    public function downloadExcel(): StreamedResponse
    {
        $movements   = $this->getFilteredMovements();
        $personaA    = $this->getPersonaName('persona_a');
        $personaB    = $this->getPersonaName('persona_b');
        $periodLabel = $this->getPeriodLabel();
        $balance     = $this->calcularBalance($movements->where('type', 'gasto'), $movements->where('type', 'ingreso'));
        $filename    = 'gastos-' . str($periodLabel)->slug() . '-' . now()->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($movements, $personaA, $personaB, $balance, $periodLabel) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            fputcsv($out, ["Control de Gastos — {$periodLabel}"], ';');
            fputcsv($out, [], ';');
            fputcsv($out, ['=== BALANCE ==='], ';');
            fputcsv($out, ['Total Gastos', number_format($balance['totalGastos'], 2, ',', '.')], ';');
            fputcsv($out, ["{$personaA} pagó", number_format($balance['gastosA'], 2, ',', '.')], ';');
            fputcsv($out, ["{$personaB} pagó", number_format($balance['gastosB'], 2, ',', '.')], ';');

            if ($balance['equilibrado']) {
                fputcsv($out, ['Balance', 'Equilibrado'], ';');
            } else {
                $deudor   = $balance['deudor'] === 'persona_a' ? $personaA : $personaB;
                $acreedor = $balance['acreedor'] === 'persona_a' ? $personaA : $personaB;
                fputcsv($out, ["{$deudor} le debe a {$acreedor}", '$' . number_format($balance['diferencia'], 2, ',', '.')], ';');
            }

            fputcsv($out, [], ';');
            fputcsv($out, ['=== MOVIMIENTOS ==='], ';');
            fputcsv($out, ['Fecha', 'Tipo', 'Categoría', 'Monto', 'Quién', 'Descripción', 'Mitad (c/u)'], ';');

            foreach ($movements as $m) {
                fputcsv($out, [
                    $m->date->format('d/m/Y'),
                    $m->type === 'gasto' ? 'Gasto' : 'Ingreso',
                    $m->category,
                    number_format($m->amount, 2, ',', '.'),
                    $m->person === 'persona_a' ? $personaA : $personaB,
                    $m->description ?? '',
                    $m->type === 'gasto' ? number_format($m->amount / 2, 2, ',', '.') : '',
                ], ';');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ─── EXPORTAR BACKUP JSON ──────────────────────────────────────
    public function downloadBackup(): StreamedResponse
    {
        $all = Movement::orderBy('date')->get()->map(fn ($m) => [
            'amount'      => (float) $m->amount,
            'type'        => $m->type,
            'category'    => $m->category,
            'description' => $m->description,
            'date'        => $m->date->format('Y-m-d'),
            'person'      => $m->person,
        ])->values()->all();

        $json = json_encode([
            'version'     => 1,
            'exported_at' => now()->toISOString(),
            'movements'   => $all,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response()->streamDownload(
            fn () => print($json),
            'backup-gastos-' . now()->format('Ymd-His') . '.json',
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }

    // ─── IMPORTAR BACKUP JSON ──────────────────────────────────────
    public function importBackup(): void
    {
        $this->importMessage = '';
        $this->importStatus  = '';

        if (blank($this->importJson)) {
            $this->importMessage = 'No se recibió ningún archivo.';
            $this->importStatus  = 'error';
            return;
        }

        try {
            $data = json_decode($this->importJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $this->importMessage = 'El archivo no es un JSON válido.';
            $this->importStatus  = 'error';
            return;
        }

        $rows = $data['movements'] ?? (array_is_list($data) ? $data : null);

        if (! $rows) {
            $this->importMessage = 'El archivo no contiene movimientos.';
            $this->importStatus  = 'error';
            return;
        }

        if ($this->importReplace) {
            Movement::truncate();
        }

        $imported = 0;
        $errors   = 0;

        foreach ($rows as $row) {
            if (! isset($row['amount'], $row['type'], $row['category'], $row['date'], $row['person'])
                || ! in_array($row['type'], ['gasto', 'ingreso'])
                || ! in_array($row['person'], ['persona_a', 'persona_b'])
            ) {
                $errors++;
                continue;
            }

            try {
                Movement::create([
                    'amount'      => (float) $row['amount'],
                    'type'        => $row['type'],
                    'category'    => $row['category'],
                    'description' => $row['description'] ?? null,
                    'date'        => $row['date'],
                    'person'      => $row['person'],
                ]);
                $imported++;
            } catch (\Exception) {
                $errors++;
            }
        }

        $this->importJson    = '';
        $this->importMessage = "Importados: {$imported}" . ($errors ? " | Con errores: {$errors}" : '');
        $this->importStatus  = $errors && ! $imported ? 'error' : 'success';
    }

    // ─── ELIMINAR TODOS ────────────────────────────────────────────
    public function deleteAll(): void
    {
        if ($this->confirmDeleteAll) {
            Movement::truncate();
            $this->confirmDeleteAll = false;
            $this->importMessage    = 'Todos los registros fueron eliminados.';
            $this->importStatus     = 'success';
        }
    }

    // ─── HELPERS ───────────────────────────────────────────────────
    private function getFilteredMovements(): Collection
    {
        return Movement::query()
            ->forPeriod($this->filterPeriodType, $this->filterYear, $this->filterMonth)
            ->forPerson($this->filterPerson ?: null)
            ->forCategory($this->filterCategory ?: null)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getPeriodLabel(): string
    {
        if ($this->filterPeriodType === 'year') {
            return "Año {$this->filterYear}";
        }

        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
        ];

        return ucfirst($meses[$this->filterMonth]) . ' ' . $this->filterYear;
    }

    private function calcularBalance(Collection $gastos, Collection $ingresos = null): array
    {
        $gastosA     = (float) $gastos->where('person', 'persona_a')->sum('amount');
        $gastosB     = (float) $gastos->where('person', 'persona_b')->sum('amount');
        $totalGastos = $gastosA + $gastosB;
        $mitad       = $totalGastos / 2;

        $ingresosA   = $ingresos ? (float) $ingresos->where('person', 'persona_a')->sum('amount') : 0;
        $ingresosB   = $ingresos ? (float) $ingresos->where('person', 'persona_b')->sum('amount') : 0;

        // Contribución neta: lo que pagó en gastos + lo que ingresó
        $diferencia  = ($gastosA + $ingresosA) - $mitad;

        return [
            'totalGastos' => $totalGastos,
            'gastosA'     => $gastosA,
            'gastosB'     => $gastosB,
            'diferencia'  => abs($diferencia),
            'deudor'      => $diferencia > 0.005 ? 'persona_b' : ($diferencia < -0.005 ? 'persona_a' : null),
            'acreedor'    => $diferencia > 0.005 ? 'persona_a' : ($diferencia < -0.005 ? 'persona_b' : null),
            'equilibrado' => abs($diferencia) < 0.005,
        ];
    }

    private function calcularEstadisticas(Collection $movements): array
    {
        $gastos        = $movements->where('type', 'gasto');
        $ingresos      = $movements->where('type', 'ingreso');
        $totalGastos   = (float) $gastos->sum('amount');
        $totalIngresos = (float) $ingresos->sum('amount');

        $porCategoria = $gastos
            ->groupBy('category')
            ->map(fn ($g) => (float) $g->sum('amount'))
            ->sortDesc()
            ->map(fn ($monto, $cat) => [
                'categoria'  => $cat,
                'monto'      => $monto,
                'porcentaje' => $totalGastos > 0 ? round($monto / $totalGastos * 100, 1) : 0,
            ])
            ->values()->all();

        $porMes = [];
        if ($this->filterPeriodType === 'year') {
            $labels = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',
                       7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
            $maxMes = 0;
            $temp   = [];
            for ($m = 1; $m <= 12; $m++) {
                $g = (float) $gastos->filter(fn ($r) => $r->date->month === $m)->sum('amount');
                $i = (float) $ingresos->filter(fn ($r) => $r->date->month === $m)->sum('amount');
                $temp[$m] = ['label' => $labels[$m], 'gastos' => $g, 'ingresos' => $i];
                $maxMes   = max($maxMes, $g, $i);
            }
            foreach ($temp as $data) {
                $porMes[] = array_merge($data, [
                    'pctGastos'   => $maxMes > 0 ? round($data['gastos'] / $maxMes * 100) : 0,
                    'pctIngresos' => $maxMes > 0 ? round($data['ingresos'] / $maxMes * 100) : 0,
                ]);
            }
        }

        $topDias = $gastos
            ->groupBy(fn ($r) => $r->date->format('Y-m-d'))
            ->map(fn ($g, $f) => ['fecha' => $f, 'monto' => (float) $g->sum('amount')])
            ->sortByDesc('monto')->take(3)->values()->all();

        return [
            'totalGastos'   => $totalGastos,
            'totalIngresos' => $totalIngresos,
            'ahorro'        => $totalIngresos - $totalGastos,
            'tasaAhorro'    => $totalIngresos > 0 ? round((1 - $totalGastos / $totalIngresos) * 100, 1) : 0,
            'porCategoria'  => $porCategoria,
            'porMes'        => $porMes,
            'topDias'       => $topDias,
            'cantGastos'    => $gastos->count(),
            'cantIngresos'  => $ingresos->count(),
            'promedioGasto' => $gastos->count() > 0 ? $totalGastos / $gastos->count() : 0,
        ];
    }

    // ─── RENDER ────────────────────────────────────────────────────
    public function render(): \Illuminate\View\View
    {
        $personaA  = $this->getPersonaName('persona_a');
        $personaB  = $this->getPersonaName('persona_b');

        $movements = $this->getFilteredMovements();
        $gastos    = $movements->where('type', 'gasto');
        $ingresos  = $movements->where('type', 'ingreso');

        $balance   = $this->calcularBalance($gastos, $ingresos);
        $stats     = $this->calcularEstadisticas($movements);

        $ingresosA = (float) $ingresos->where('person', 'persona_a')->sum('amount');
        $ingresosB = (float) $ingresos->where('person', 'persona_b')->sum('amount');

        // Categorías dinámicas desde BD
        $categoriasActual = Category::where('type', $this->type)->ordenadas()->pluck('name')->toArray();
        $categoriasGasto  = Category::gastos()->ordenadas()->pluck('name')->toArray();
        $categoriasIngreso = Category::ingresos()->ordenadas()->pluck('name')->toArray();

        $todasCategorias = collect($categoriasGasto)->merge($categoriasIngreso)->unique()->sort()->values();

        // Categorías para el tab de config
        $configCats = Category::where('type', $this->configCatType)->ordenadas()->get();

        return view('livewire.dashboard', [
            'movements'        => $movements,
            'balance'          => $balance,
            'stats'            => $stats,
            'ingresosA'        => $ingresosA,
            'ingresosB'        => $ingresosB,
            'totalIngresos'    => $ingresosA + $ingresosB,
            'periodLabel'      => $this->getPeriodLabel(),
            'personaA'         => $personaA,
            'personaB'         => $personaB,
            'categoriasActual' => $categoriasActual,
            'todasCategorias'  => $todasCategorias,
            'configCats'       => $configCats,
            'totalRegistros'   => Movement::count(),
            'meses'            => [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
            ],
        ]);
    }
}
