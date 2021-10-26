<?php

namespace Devolon\Messente\Tests\Services;

use Devolon\Messente\DTOs\MessenteConfigDTO;
use Devolon\Messente\Services\GetMessenteConfigService;
use Devolon\Messente\Tests\MessenteTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class GetMessenteConfigServiceTest extends MessenteTestCase
{
    use WithFaker;

    public function testItReturnsDTOFrom()
    {
        // Arrange
        $config = [
            'url' => $this->faker->url,
            'username' => $this->faker->userName,
            'password' => $this->faker->password(11) . 'P#',
            'from' => $this->faker->name,
        ];

        config([
            'messente' => $config,
        ]);

        $service = $this->resolveService();
        $expectedDTO = MessenteConfigDTO::fromArray($config);

        // Act
        $result = $service();

        // Assert
        $this->assertEquals($expectedDTO, $result);
    }

    private function resolveService(): GetMessenteConfigService
    {
        return resolve(GetMessenteConfigService::class);
    }
}
