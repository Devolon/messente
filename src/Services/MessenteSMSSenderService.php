<?php

namespace Devolon\Messente\Services;

use Devolon\Sms\DTOs\SentSMSMessageDTO;
use Devolon\Sms\DTOs\SMSMessageDTO;
use Devolon\Sms\Exceptions\SendingSMSFailed;
use Devolon\Sms\Services\Contracts\SMSSenderServiceInterface;
use Messente\Api\ApiException;
use Messente\Api\Model\Omnimessage;
use Messente\Api\Model\SMS;

class MessenteSMSSenderService implements SMSSenderServiceInterface
{
    public function __construct(
        private CreateOmniMessageApiService $createOmniMessageApiService,
        private GetMessenteConfigService $getMessenteConfigService,
    ) {
    }

    /**
     * @throws SendingSMSFailed
     */
    public function __invoke(SMSMessageDTO $smsMessageDTO): SentSMSMessageDTO
    {
        $messenteConfigDTO = ($this->getMessenteConfigService)();
        $omniMessageAPI = ($this->createOmniMessageApiService)($messenteConfigDTO);

        $sms = new SMS([
            'text' => $smsMessageDTO->text,
            'sender' => $messenteConfigDTO->from
        ]);


        try {
            $response = $omniMessageAPI->sendOmnimessage(
                new Omnimessage([
                    'to' => $smsMessageDTO->to,
                    'messages' => [$sms]
                ])
            );

            return SentSMSMessageDTO::fromArray([
                'from' => $messenteConfigDTO->from,
                'to' => $smsMessageDTO->to,
                'text' => $smsMessageDTO->text,
                'tracking_code' => $response->getOmnimessageId()
            ]);
        } catch (ApiException $exception) {
            throw new SendingSMSFailed($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public static function getName(): string
    {
        return 'messente';
    }
}
