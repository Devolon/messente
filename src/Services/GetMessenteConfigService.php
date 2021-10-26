<?php

namespace Devolon\Messente\Services;

use Devolon\Messente\DTOs\MessenteConfigDTO;

class GetMessenteConfigService
{
    public function __invoke(): MessenteConfigDTO
    {
        return MessenteConfigDTO::fromArray(config('messente'));
    }
}
