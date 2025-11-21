<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Word;
use Illuminate\Database\Seeder;

class WordSeeder extends Seeder
{
    /**
     * Technical terms with their translations
     */
    private array $techWords = [
        [
            'english' => 'Computer',
            'spanish' => 'Computadora / Ordenador',
            'german' => 'Computer / Rechner',
        ],
        [
            'english' => 'Algorithm',
            'spanish' => 'Algoritmo',
            'german' => 'Algorithmus',
        ],
        [
            'english' => 'Database',
            'spanish' => 'Base de datos',
            'german' => 'Datenbank',
        ],
        [
            'english' => 'Network',
            'spanish' => 'Red',
            'german' => 'Netzwerk',
        ],
        [
            'english' => 'Server',
            'spanish' => 'Servidor',
            'german' => 'Server',
        ],
        [
            'english' => 'Protocol',
            'spanish' => 'Protocolo',
            'german' => 'Protokoll',
        ],
        [
            'english' => 'Interface',
            'spanish' => 'Interfaz',
            'german' => 'Schnittstelle',
        ],
        [
            'english' => 'Framework',
            'spanish' => 'Marco de trabajo',
            'german' => 'Framework',
        ],
        [
            'english' => 'Repository',
            'spanish' => 'Repositorio',
            'german' => 'Repository',
        ],
        [
            'english' => 'API',
            'spanish' => 'Interfaz de programación de aplicaciones',
            'german' => 'Programmierschnittstelle',
        ],
        [
            'english' => 'Cache',
            'spanish' => 'Caché',
            'german' => 'Cache',
        ],
        [
            'english' => 'Container',
            'spanish' => 'Contenedor',
            'german' => 'Container',
        ],
        [
            'english' => 'Deployment',
            'spanish' => 'Despliegue',
            'german' => 'Bereitstellung',
        ],
        [
            'english' => 'Middleware',
            'spanish' => 'Middleware',
            'german' => 'Middleware',
        ],
        [
            'english' => 'Queue',
            'spanish' => 'Cola',
            'german' => 'Warteschlange',
        ],
        [
            'english' => 'Authentication',
            'spanish' => 'Autenticación',
            'german' => 'Authentifizierung',
        ],
        [
            'english' => 'Authorization',
            'spanish' => 'Autorización',
            'german' => 'Autorisierung',
        ],
        [
            'english' => 'Encryption',
            'spanish' => 'Encriptación',
            'german' => 'Verschlüsselung',
        ],
        [
            'english' => 'Firewall',
            'spanish' => 'Cortafuegos',
            'german' => 'Firewall',
        ],
        [
            'english' => 'Gateway',
            'spanish' => 'Puerta de enlace',
            'german' => 'Gateway',
        ],
        [
            'english' => 'Router',
            'spanish' => 'Enrutador',
            'german' => 'Router',
        ],
        [
            'english' => 'Bandwidth',
            'spanish' => 'Ancho de banda',
            'german' => 'Bandbreite',
        ],
        [
            'english' => 'Cloud Computing',
            'spanish' => 'Computación en la nube',
            'german' => 'Cloud Computing',
        ],
        [
            'english' => 'Microservice',
            'spanish' => 'Microservicio',
            'german' => 'Microservice',
        ],
        [
            'english' => 'Load Balancer',
            'spanish' => 'Balanceador de carga',
            'german' => 'Lastverteiler',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->techWords as $wordData) {
            $word = Word::create([
                'english_word' => $wordData['english'],
            ]);

            // Create English translation
            $word->translations()->create([
                'language' => 'en',
                'translation' => $wordData['english'],
            ]);

            // Create Spanish translation
            $word->translations()->create([
                'language' => 'es',
                'translation' => $wordData['spanish'],
            ]);

            // Create German translation
            $word->translations()->create([
                'language' => 'de',
                'translation' => $wordData['german'],
            ]);
        }

        $this->command->info('Seeded ' . count($this->techWords) . ' tech words with translations');
    }
}
