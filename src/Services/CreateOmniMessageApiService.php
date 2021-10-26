<?php

namespace Devolon\Messente\Services;

use Devolon\Messente\DTOs\MessenteConfigDTO;
use Messente\Api\Api\OmnimessageApi;
use Messente\Api\Configuration;

class CreateOmniMessageApiService
{
    public function __invoke(MessenteConfigDTO $configDTO): OmnimessageApi
    {
        $config = Configuration::getDefaultConfiguration()
            ->setUsername($configDTO->username)
            ->setPassword($configDTO->password);

        return new OmnimessageApi(config: $config);
    }
}
