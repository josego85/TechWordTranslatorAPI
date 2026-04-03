<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Word;
use Illuminate\Database\Seeder;

class WordSeeder extends Seeder
{
    /**
     * Technical terms with their translations and categories.
     *
     * @var list<array{english: string, spanish: string, german: string, categories: list<string>}>
     */
    private array $techWords = [
        [
            'english' => 'Computer',
            'spanish' => 'Computadora / Ordenador',
            'german' => 'Computer / Rechner',
            'categories' => ['hardware'],
        ],
        [
            'english' => 'Algorithm',
            'spanish' => 'Algoritmo',
            'german' => 'Algorithmus',
            'categories' => ['algorithms'],
        ],
        [
            'english' => 'Database',
            'spanish' => 'Base de datos',
            'german' => 'Datenbank',
            'categories' => ['databases'],
        ],
        [
            'english' => 'Network',
            'spanish' => 'Red',
            'german' => 'Netzwerk',
            'categories' => ['networking'],
        ],
        [
            'english' => 'Server',
            'spanish' => 'Servidor',
            'german' => 'Server',
            'categories' => ['networking', 'hardware'],
        ],
        [
            'english' => 'Protocol',
            'spanish' => 'Protocolo',
            'german' => 'Protokoll',
            'categories' => ['networking'],
        ],
        [
            'english' => 'Interface',
            'spanish' => 'Interfaz',
            'german' => 'Schnittstelle',
            'categories' => ['programming-languages', 'web'],
        ],
        [
            'english' => 'Framework',
            'spanish' => 'Marco de trabajo',
            'german' => 'Framework',
            'categories' => ['programming-languages', 'web'],
        ],
        [
            'english' => 'Repository',
            'spanish' => 'Repositorio',
            'german' => 'Repository',
            'categories' => ['programming-languages', 'devops'],
        ],
        [
            'english' => 'API',
            'spanish' => 'Interfaz de programación de aplicaciones',
            'german' => 'Programmierschnittstelle',
            'categories' => ['web', 'networking'],
        ],
        [
            'english' => 'Cache',
            'spanish' => 'Caché',
            'german' => 'Cache',
            'categories' => ['databases', 'operating-systems'],
        ],
        [
            'english' => 'Container',
            'spanish' => 'Contenedor',
            'german' => 'Container',
            'categories' => ['devops', 'operating-systems', 'cloud'],
        ],
        [
            'english' => 'Deployment',
            'spanish' => 'Despliegue',
            'german' => 'Bereitstellung',
            'categories' => ['devops', 'cloud'],
        ],
        [
            'english' => 'Middleware',
            'spanish' => 'Middleware',
            'german' => 'Middleware',
            'categories' => ['web', 'networking'],
        ],
        [
            'english' => 'Queue',
            'spanish' => 'Cola',
            'german' => 'Warteschlange',
            'categories' => ['algorithms', 'operating-systems'],
        ],
        [
            'english' => 'Authentication',
            'spanish' => 'Autenticación',
            'german' => 'Authentifizierung',
            'categories' => ['security'],
        ],
        [
            'english' => 'Authorization',
            'spanish' => 'Autorización',
            'german' => 'Autorisierung',
            'categories' => ['security'],
        ],
        [
            'english' => 'Encryption',
            'spanish' => 'Encriptación',
            'german' => 'Verschlüsselung',
            'categories' => ['security', 'algorithms'],
        ],
        [
            'english' => 'Firewall',
            'spanish' => 'Cortafuegos',
            'german' => 'Firewall',
            'categories' => ['security', 'networking'],
        ],
        [
            'english' => 'Gateway',
            'spanish' => 'Puerta de enlace',
            'german' => 'Gateway',
            'categories' => ['networking'],
        ],
        [
            'english' => 'Router',
            'spanish' => 'Enrutador',
            'german' => 'Router',
            'categories' => ['networking', 'hardware'],
        ],
        [
            'english' => 'Bandwidth',
            'spanish' => 'Ancho de banda',
            'german' => 'Bandbreite',
            'categories' => ['networking'],
        ],
        [
            'english' => 'Cloud Computing',
            'spanish' => 'Computación en la nube',
            'german' => 'Cloud Computing',
            'categories' => ['cloud', 'devops'],
        ],
        [
            'english' => 'Microservice',
            'spanish' => 'Microservicio',
            'german' => 'Microservice',
            'categories' => ['web', 'devops', 'cloud'],
        ],
        [
            'english' => 'Load Balancer',
            'spanish' => 'Balanceador de carga',
            'german' => 'Lastverteiler',
            'categories' => ['networking', 'devops', 'cloud'],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryMap = Category::all()->keyBy('slug');

        foreach ($this->techWords as $wordData) {
            $word = Word::create([
                'english_word' => $wordData['english'],
            ]);

            $word->translations()->create([
                'language' => 'en',
                'translation' => $wordData['english'],
            ]);

            $word->translations()->create([
                'language' => 'es',
                'translation' => $wordData['spanish'],
            ]);

            $word->translations()->create([
                'language' => 'de',
                'translation' => $wordData['german'],
            ]);

            $categoryIds = collect($wordData['categories'])
                ->map(fn (string $slug) => $categoryMap->get($slug)?->id)
                ->filter()
                ->all();

            $word->categories()->sync($categoryIds);
        }

        $this->command->info('Seeded ' . count($this->techWords) . ' tech words with translations and categories');
    }
}
