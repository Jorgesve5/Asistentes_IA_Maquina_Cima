<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Manual;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Admin User
        User::updateOrCreate(
            ['email' => 'admin'],
            [
                'name' => 'Supervisor CIMA',
                'password' => bcrypt('1234'),
            ]
        );

        // 8 machines from original Next.js data
        $machines = [
            [
                'id' => 'zeta-633l',
                'name' => 'MÁQUINA ZETA 633L',
                'status' => 'online',
                'serial' => '2024/I/8881',
                'indicator' => '++',
                'column' => 1,
                'row' => 1,
                'subLabel' => 'PREMONTAJE ZETA INYECTORA',
                'manuals' => [
                    ['fileName' => 'Manual_Operacion_ZETA_633L.pdf', 'size' => '14.2 MB', 'text' => 'Procedimiento de encendido de la máquina Zeta 633L. Verificar presión de aire a 6 bar. Presionar botón verde. Limpiar cabezal inyector diariamente.'],
                    ['fileName' => 'Guia_Mantenimiento_ZETA_633L.pdf', 'size' => '6.8 MB', 'text' => 'Lubricación de guías lineales cada 500 horas de uso. Grasa recomendada: Mobilux EP 2. Código de error E-22 indica sobrecalentamiento del motor principal. Solución: Dejar enfriar y resetear térmico.']
                ]
            ],
            [
                'id' => 'powerstrip-9500',
                'name' => 'MÁQUINA POWERSTRIP 9500',
                'status' => 'warning',
                'serial' => '2025/PS/921',
                'indicator' => '',
                'column' => 1,
                'row' => 2,
                'subLabel' => 'PREMONTAJE MANGUERAS',
                'manuals' => [
                    ['fileName' => 'Powerstrip_9500_User_Manual.pdf', 'size' => '22.5 MB', 'text' => 'Ajuste de rodillos de arrastre. Cuchillas de corte deben afilarse cada 100,000 cortes. Error E-04 es atasco de cable. Compruebe la guía de entrada de mangueras.']
                ]
            ],
            [
                'id' => 'zeta-xl',
                'name' => 'MAQUINA ZETA XL',
                'status' => 'online',
                'serial' => '2026/2715',
                'indicator' => '++',
                'column' => 2,
                'row' => 1,
                'subLabel' => '',
                'manuals' => [
                    ['fileName' => 'Manual_Usuario_ZetaXL.pdf', 'size' => '18.4 MB', 'text' => 'Módulo de ensamble Zeta XL. Presión neumática recomendada: 6.5 bar. Calibración de terminales mediante panel táctil.']
                ]
            ],
            [
                'id' => 'kappa-340',
                'name' => 'MÁQUINA KAPPA 340',
                'status' => 'online',
                'serial' => '2026/3988',
                'indicator' => '',
                'column' => 2,
                'row' => 2,
                'subLabel' => 'MAQUINA SOLUCIÓN MODULAR',
                'manuals' => [
                    ['fileName' => 'Kappa_340_Technical_Specs.pdf', 'size' => '12.1 MB', 'text' => 'Especificaciones técnicas Kappa 340. Capacidad de corte: 0.05 a 10 mm². Detección de nudos en cable de entrada.']
                ]
            ],
            [
                'id' => 'alpha-433s',
                'name' => 'MÁQUINA ALPHA 433S',
                'status' => 'online',
                'serial' => '2024/I/8881',
                'indicator' => '++',
                'column' => 3,
                'row' => 1,
                'subLabel' => 'PREMONTAJE ALPHA',
                'manuals' => [
                    ['fileName' => 'Alpha_433S_Manual_Operario.pdf', 'size' => '10.5 MB', 'text' => 'Puesta en marcha de la Alpha 433S. Comprobar alineación de las estaciones de crimpado. Código E-12 indica presión insuficiente.']
                ]
            ],
            [
                'id' => 'kappa-350',
                'name' => 'MÁQUINA KAPPA 350',
                'status' => 'online',
                'serial' => '2026/3596',
                'indicator' => '',
                'column' => 3,
                'row' => 2,
                'subLabel' => 'LISTADO OF KAPPA',
                'manuals' => [
                    ['fileName' => 'Kappa_350_Service_Guide.pdf', 'size' => '15.9 MB', 'text' => 'Guía de servicio Kappa 350. Reemplazo de rodillos de alimentación. Ajuste de cuchilla rotativa para desforre parcial.']
                ]
            ],
            [
                'id' => 'alpha-565',
                'name' => 'MAQUINA ALPHA 565',
                'status' => 'online',
                'serial' => '2024/I/8881',
                'indicator' => '',
                'column' => 4,
                'row' => 1,
                'subLabel' => 'LISTADO DE OF MAQUINAS',
                'manuals' => [
                    ['fileName' => 'Alpha_565_Setup_Instructions.pdf', 'size' => '8.3 MB', 'text' => 'Configuración de doble crimpado en la Alpha 565. Ajustar altura de crimpado a 1.25 mm para terminales CIMA.']
                ]
            ],
            [
                'id' => 'crimp-center-63',
                'name' => 'MÁQUINA CRIMP CENTER 63',
                'status' => 'warning',
                'serial' => '2025/CC/063',
                'indicator' => '',
                'column' => 4,
                'row' => 2,
                'subLabel' => 'PREMONTAJE CC63',
                'manuals' => [
                    ['fileName' => 'CrimpCenter_63_Troubleshooting.pdf', 'size' => '24.1 MB', 'text' => 'Solución de problemas CrimpCenter 63. Fallo de sellado por obstrucción neumática. Lubricar mecanismo de transferencia.']
                ]
            ]
        ];

        foreach ($machines as $m) {
            $machineManuals = $m['manuals'] ?? [];
            unset($m['manuals']);

            $machine = Machine::updateOrCreate(['id' => $m['id']], $m);

            foreach ($machineManuals as $man) {
                Manual::updateOrCreate(
                    ['machine_id' => $machine->id, 'fileName' => $man['fileName']],
                    $man
                );
            }
        }
    }
}
