<?php

declare(strict_types=1);

namespace App\Support\Csp;

use Spatie\Csp\Policies\Basic;

class ContentPolicy extends Basic
{
    #[\Override]
    public function configure()
    {
        parent::configure();
    }
}
