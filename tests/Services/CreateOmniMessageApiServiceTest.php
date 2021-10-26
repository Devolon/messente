<?php

namespace Devolon\Messente\Tests\Services;

use Devolon\Messente\DTOs\MessenteConfigDTO;
use Devolon\Messente\Services\CreateOmniMessageApiService;
use Devolon\Messente\Tests\MessenteTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Messente\Api\Api\OmnimessageApi;
use Messente\Api\Configuration;

class CreateOmniMessageApiServiceTest extends MessenteTestCase
{
    use WithFaker;

    public function testInvokeReturnsInstanceOfOmniMessageApi()
    {
        // Arrange
        $configDTO = MessenteConfigDTO::fromArray([
            'url' => '',
            'username' => $this->faker->userName,
            'password' => $this->faker->password(11) . 'P#',
            'from' => $this->faker->name,
        ]);

        $service = $this->resolveService();

        $expectedMessenteConfiguration = Configuration::getDefaultConfiguration()
            ->setUsername($configDTO->username)
            ->setPassword($configDTO->password)
        ;

        // Act
        $result = $service($configDTO);

        // Assert
        $this->assertInstanceOf(OmnimessageApi::class, $result);
        $this->assertEquals($expectedMessenteConfiguration, $result->getConfig());
    }

    private function resolveService(): CreateOmniMessageApiService
    {
        return resolve(CreateOmniMessageApiService::class);
    }
}
