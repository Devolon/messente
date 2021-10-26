<?php

namespace Devolon\Messente\Tests;

use Devolon\Sms\Services\Contracts\SMSSenderServiceInterface;
use Devolon\Messente\Services\MessenteSMSSenderService;
use Devolon\Sms\Services\ResolveSMSSenderService;
use Traversable;

class MessenteServiceProviderTest extends MessenteTestCase
{
    public function testItTagMessenteService()
    {
        // Act
        $messenteSMSSenderService = $this->resolveMessenteSMSSenderService();

        /** @var Traversable $result */
        $result = $this->app->tagged(SMSSenderServiceInterface::class);

        // Assert
        $this->assertContains($messenteSMSSenderService, iterator_to_array($result));
    }

    public function testMessenteIsBeenAdded()
    {
        // Arrange
        $smsSenderResolver = $this->resolveSMSSenderResolver();

        // Act
        $result = $smsSenderResolver('messente');

        // Assert
        $this->assertInstanceOf(MessenteSMSSenderService::class, $result);
    }

    private function resolveMessenteSMSSenderService(): MessenteSMSSenderService
    {
        return resolve(MessenteSMSSenderService::class);
    }

    private function resolveSMSSenderResolver(): ResolveSMSSenderService
    {
        return resolve(ResolveSMSSenderService::class);
    }
}
