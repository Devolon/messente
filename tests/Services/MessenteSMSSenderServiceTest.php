<?php

namespace Devolon\Messente\Tests\Services;

use Devolon\Messente\Tests\MessenteTestCase;
use Devolon\Sms\DTOs\SentSMSMessageDTO;
use Devolon\Sms\DTOs\SMSMessageDTO;
use Devolon\Messente\DTOs\MessenteConfigDTO;
use Devolon\Messente\Services\CreateOmniMessageApiService;
use Devolon\Messente\Services\GetMessenteConfigService;
use Devolon\Messente\Services\MessenteSMSSenderService;
use Illuminate\Foundation\Testing\WithFaker;
use Messente\Api\Api\OmnimessageApi;
use Messente\Api\ApiException;
use Messente\Api\Model\Omnimessage;
use Messente\Api\Model\OmniMessageCreateSuccessResponse;
use Messente\Api\Model\SMS;
use Mockery;
use Mockery\MockInterface;
use Devolon\Sms\Exceptions\SendingSMSFailed;

class MessenteSMSSenderServiceTest extends MessenteTestCase
{
    use WithFaker;

    public function testItSendsSMSSuccessfully()
    {
        // Arrange
        $omniMessageApi = $this->mockOmniMessageApi();
        $omniMessageResponse = $this->mockOmniMessageResponse();
        $createOmniMessageApiService = $this->mockCreateOmniMessageApiService();
        $getMessenteConfigService = $this->mockGetMessenteConfigService();
        $service = $this->resolveService();
        $trackingCode = $this->faker->uuid;
        $smsMessageDTO = SMSMessageDTO::fromArray([
            'to' => $this->faker->e164PhoneNumber,
            'text' => $this->faker->text,
        ]);
        $messenteConfigDTO = MessenteConfigDTO::fromArray([
            'url' => '',
            'username' => $this->faker->userName,
            'password' => $this->faker->password(11) . 'P#',
            'from' => $this->faker->name,
        ]);

        // Expect
        $getMessenteConfigService
            ->shouldReceive('__invoke')
            ->once()
            ->andReturn($messenteConfigDTO);

        $createOmniMessageApiService
            ->shouldReceive('__invoke')
            ->once()
            ->with($messenteConfigDTO)
            ->andReturn($omniMessageApi);

        $omniMessageResponse
            ->shouldReceive('getOmnimessageId')
            ->once()
            ->andReturn($trackingCode);

        $omniMessageApi
            ->shouldReceive('sendOmnimessage')
            ->withArgs(function (Omnimessage $omniMessage) use ($smsMessageDTO, $messenteConfigDTO) {
                if (1 !== count($omniMessage->getMessages())) {
                    return false;
                }

                $sms = $omniMessage->getMessages()[0];
                if (!$sms instanceof SMS) {
                    return false;
                }

                return $omniMessage->getTo() === $smsMessageDTO->to &&
                    $smsMessageDTO->text && $sms->getText() &&
                    $messenteConfigDTO->from === $sms->getSender();
            })
            ->once()
            ->andReturn($omniMessageResponse);

        // Act
        $result = $service($smsMessageDTO);

        // Assert
        $this->assertInstanceOf(SentSMSMessageDTO::class, $result);
        $this->assertEquals(SentSMSMessageDTO::fromArray([
            'from' => $messenteConfigDTO->from,
            'to' => $smsMessageDTO->to,
            'text' => $smsMessageDTO->text,
            'tracking_code' => $trackingCode,
        ]), $result);
    }

    public function testItHandlesExceptionsOfOmniMessageApiAndThrowsItsException()
    {
        // Arrange
        $omniMessageApi = $this->mockOmniMessageApi();
        $createOmniMessageApiService = $this->mockCreateOmniMessageApiService();
        $getMessenteConfigService = $this->mockGetMessenteConfigService();
        $service = $this->resolveService();
        $smsMessageDTO = SMSMessageDTO::fromArray([
            'to' => $this->faker->e164PhoneNumber,
            'text' => $this->faker->text
        ]);
        $messenteConfigDTO = MessenteConfigDTO::fromArray([
            'url' => '',
            'username' => $this->faker->userName,
            'password' => $this->faker->password(11) . 'P#',
            'from' => $this->faker->name,
        ]);

        // Expect
        $getMessenteConfigService
            ->shouldReceive('__invoke')
            ->once()
            ->andReturn($messenteConfigDTO);

        $createOmniMessageApiService
            ->shouldReceive('__invoke')
            ->once()
            ->with($messenteConfigDTO)
            ->andReturn($omniMessageApi);

        $omniMessageApi
            ->shouldReceive('sendOmnimessage')
            ->withArgs(function (Omnimessage $omniMessage) use ($smsMessageDTO, $messenteConfigDTO) {
                if (1 !== count($omniMessage->getMessages())) {
                    return false;
                }

                $sms = $omniMessage->getMessages()[0];
                if (!$sms instanceof SMS) {
                    return false;
                }

                return $omniMessage->getTo() === $smsMessageDTO->to &&
                    $smsMessageDTO->text && $sms->getText() &&
                    $messenteConfigDTO->from === $sms->getSender();
            })
            ->once()
            ->andThrow(ApiException::class);

        // Expect
        $this->expectException(SendingSMSFailed::class);

        // Act
        $service($smsMessageDTO);
    }

    private function mockOmniMessageApi(): OmnimessageApi | MockInterface
    {
        return Mockery::mock(OmnimessageApi::class);
    }

    private function resolveService(): MessenteSMSSenderService
    {
        return resolve(MessenteSMSSenderService::class);
    }

    private function mockCreateOmniMessageApiService(): CreateOmniMessageApiService | MockInterface
    {
        return $this->mock(CreateOmniMessageApiService::class);
    }

    private function mockGetMessenteConfigService(): GetMessenteConfigService | MockInterface
    {
        return $this->mock(GetMessenteConfigService::class);
    }

    private function mockOmniMessageResponse(): OmniMessageCreateSuccessResponse | MockInterface
    {
        return $this->mock(OmniMessageCreateSuccessResponse::class);
    }
}
