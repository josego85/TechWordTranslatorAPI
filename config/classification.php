<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Ollama model used for word classification
    |--------------------------------------------------------------------------
    | Requires Ollama installed natively on the host:
    |   curl -fsSL https://ollama.com/install.sh | sh
    |   ollama pull llama3.2
    |
    | Connect from Docker: set OLLAMA_URL=http://host.docker.internal:11434
    | in your .env (Mac/Windows) or http://172.17.0.1:11434 (Linux).
    */
    'model' => env('OLLAMA_MODEL', 'llama3.2'),
];
