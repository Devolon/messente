<?php

namespace Devolon\Messente\Tests\DTO\MessenteConfigDTO;

use Devolon\Messente\DTOs\MessenteConfigDTO;
use Devolon\Messente\Tests\MessenteTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class ConstructorTest extends MessenteTestCase
{
    use WithFaker;

    public function testItHasSupposedAttributes()
    {
        // Arrange
        $data = [
            'url' => $this->faker->url,
            'username' => $this->faker->userName,
            'password' => $this->faker->password(11) . 'P#',
            'from' => $this->faker->name,
        ];

        // Act
        $result = MessenteConfigDTO::fromArray($data);

        // Assert
        $this->assertInstanceOf(MessenteConfigDTO::class, $result);
        $this->assertEquals($data['url'], $result->url);
        $this->assertEquals($data['username'], $result->username);
        $this->assertEquals($data['password'], $result->password);
        $this->assertEquals($data['from'], $result->from);
    }
}
