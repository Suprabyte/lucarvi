<?php

namespace App\Filament\Resources\Ausencias\Pages;

use App\Filament\Resources\Ausencias\AusenciaResource;
use App\Models\Ausencia;
use App\Models\Empleado;
use App\Models\SuspensionTipo;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ListAusencias extends ListRecords
{
    protected static string $resource = AusenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importar_excel')
                ->label('Importar Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    FileUpload::make('excel_file')
                        ->label('Archivo Excel')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->maxSize(10240) // 10MB
                        ->required()
                        ->helperText('Sube un archivo Excel con los datos de ausencias. El archivo debe contener una hoja llamada "DATOS" (.xlsx o .xls, máximo 10MB)')
                ])
                ->modalSubmitActionLabel('Procesar')
                ->modalHeading('Importar Ausencias desde Excel')
                ->modalDescription('Selecciona un archivo Excel para importar los datos de ausencias automáticamente.')
                ->action(function (array $data) {
                    $this->procesarExcel($data);
                }),
            CreateAction::make(),
        ];
    }

    private function procesarExcel(array $data)
    {
        if (!isset($data['excel_file']) || empty($data['excel_file'])) {
            Notification::make()
                ->title('Error')
                ->body('Por favor selecciona un archivo Excel')
                ->danger()
                ->send();
            return;
        }

        try {
            $file = $data['excel_file'];
            
            if (is_string($file)) {
                $fileName = $file;
            } elseif (is_array($file) && !empty($file)) {
                $fileName = $file[0] ?? '';
            } else {
                throw new \Exception('Formato de archivo no reconocido');
            }
            
            $filePath = storage_path('app/private/' . $fileName);
            
            if (!file_exists($filePath)) {
                throw new \Exception('No se pudo encontrar el archivo subido');
            }
            
            // Leer el archivo Excel
            $spreadsheet = IOFactory::load($filePath);
            
            // Buscar la hoja "DATOS"
            $worksheet = $spreadsheet->getSheetByName('DATOS');
            
            if ($worksheet === null) {
                // Si no encuentra la hoja "DATOS", listar las hojas disponibles
                $hojas = [];
                foreach ($spreadsheet->getAllSheets() as $hoja) {
                    $hojas[] = $hoja->getTitle();
                }
                $listaHojas = implode(', ', $hojas);
                throw new \Exception("El archivo debe contener una hoja llamada 'DATOS'. Hojas encontradas: {$listaHojas}");
            }
            
            // Obtener el año de la celda B2
            $añoRaw = $worksheet->getCell('B2')->getValue();
            $año = (int) preg_replace('/[^0-9]/', '', (string)$añoRaw);
            if ($año < 1900) {
                $año = (int) date('Y');
            }

            // Obtener el mes de la celda A2
            $mesTexto = trim((string)$worksheet->getCell('A2')->getValue());
            $mes = $this->convertirMesANumero($mesTexto);
            
            $ausenciasData = [];
            
            // Buscar la fila donde está "CARGO"
            $filaEncabezado = 4;
            $cargoCell = $worksheet->getCell('B' . $filaEncabezado)->getValue();
            
            if (trim($cargoCell) !== 'CARGO') {
                for ($i = 1; $i <= 10; $i++) {
                    if (trim($worksheet->getCell('B' . $i)->getValue()) === 'CARGO') {
                        $filaEncabezado = $i;
                        break;
                    }
                }
            }
            
            // Leer trabajadores desde la fila siguiente al encabezado
            $filaInicio = $filaEncabezado + 1;
            $maxRow = $worksheet->getHighestRow();
            
            for ($row = $filaInicio; $row <= $maxRow; $row++) {
                // Obtener DNI de la columna H
                $dni = $worksheet->getCell('H' . $row)->getValue();
                
                if (empty($dni) || !is_numeric($dni)) {
                    continue;
                }
                
                $diasMes = max(1, (int) @cal_days_in_month(CAL_GREGORIAN, max(1, min(12, $mes)), max(1900, $año)));
                $diasMes = min(31, $diasMes);

                // Detectar columna del día 1
                $startInfo = $this->detectarColumnaDia1($worksheet);
                if ($startInfo) {
                    $startColIndex = $startInfo['colIndex'];
                } else {
                    $startColIndex = Coordinate::columnIndexFromString('U');
                }

                // Leer cada día
                for ($dia = 1; $dia <= $diasMes; $dia++) {
                    $colIndex = $startColIndex + ($dia - 1);
                    $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                    $codigo = trim((string)$worksheet->getCell($colLetter . $row)->getValue());
                    
                    $codigoLimpio = strtoupper(trim($codigo));
                    
                    // Solo procesar si hay un código de ausencia válido (NO si es presente)
                    if ($codigoLimpio !== '' && $codigoLimpio !== '-' && $codigoLimpio !== 'X' && $codigoLimpio !== '0') {
                        $fecha = Carbon::createFromDate($año, $mes, $dia)->format('Y-m-d');

                        $ausenciasData[] = [
                            'fecha' => $fecha,
                            'dni' => (int)$dni,
                            'codigo' => $codigoLimpio
                        ];
                    }
                }
            }
            
            $this->procesarAusencias($ausenciasData);
            
            // Limpiar archivo temporal
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al procesar el archivo')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function procesarAusencias(array $ausenciasData)
    {
        $procesados = 0;
        $duplicados = 0;
        $errores = 0;

        foreach ($ausenciasData as $item) {
            try {
                // Buscar empleado por DNI
                $empleado = Empleado::where('dni', $item['dni'])->first();
                
                if (!$empleado) {
                    $errores++;
                    continue;
                }

                // Buscar el código directamente en la tabla suspension_tipos
                $suspensionTipo = SuspensionTipo::where('codigo', $item['codigo'])->first();
                
                if (!$suspensionTipo) {
                    $errores++;
                    continue;
                }
                
                // Usar el tipo y descripción de la tabla
                $tipoMapeado = $suspensionTipo->tipo ?? 'OTRO';
                $motivo = $suspensionTipo->descripcion;

                // Verificar si ya existe la ausencia
                $existeAusencia = Ausencia::where('empleado_id', $empleado->id)
                    ->where('fecha', $item['fecha'])
                    ->where('tipo', $tipoMapeado)
                    ->exists();

                if ($existeAusencia) {
                    $duplicados++;
                    continue;
                }

                // Crear la ausencia
                Ausencia::create([
                    'empleado_id' => $empleado->id,
                    'fecha' => $item['fecha'],
                    'tipo' => $tipoMapeado,
                    'motivo' => $motivo
                ]);

                $procesados++;

            } catch (\Exception $e) {
                $errores++;
            }
        }

        // Mostrar resultados
        $mensaje = "Registros procesados: {$procesados}";
        if ($duplicados > 0) {
            $mensaje .= " | Duplicados omitidos: {$duplicados}";
        }
        if ($errores > 0) {
            $mensaje .= " | Errores: {$errores}";
        }

        Notification::make()
            ->title('Importación completada')
            ->body($mensaje)
            ->success()
            ->send();
    }

    private function convertirMesANumero($mesTexto)
    {
        $meses = [
            'ENERO' => 1, 'FEBRERO' => 2, 'MARZO' => 3, 'ABRIL' => 4,
            'MAYO' => 5, 'JUNIO' => 6, 'JULIO' => 7, 'AGOSTO' => 8,
            'SETIEMBRE' => 9, 'SEPTIEMBRE' => 9, 'OCTUBRE' => 10,
            'NOVIEMBRE' => 11, 'DICIEMBRE' => 12
        ];
        
        return $meses[strtoupper($mesTexto)] ?? 1;
    }

    private function detectarColumnaDia1($worksheet)
    {
        $maxSearchRow = 6;
        $highestColumn = $worksheet->getHighestColumn();
        $highestIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($r = 1; $r <= $maxSearchRow; $r++) {
            for ($c = 1; $c <= $highestIndex; $c++) {
                $colLetter = Coordinate::stringFromColumnIndex($c);
                $raw = (string) $worksheet->getCell($colLetter . $r)->getValue();
                $val = trim($raw);
                if ($val === '') {
                    continue;
                }

                $digits = preg_replace('/[^0-9]/', '', $val);
                if ($digits === '') {
                    continue;
                }

                $num = (int) $digits;
                if ($num === 1) {
                    return ['colIndex' => $c, 'row' => $r];
                }
            }
        }

        return null;
    }


}
