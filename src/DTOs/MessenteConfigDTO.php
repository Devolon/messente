<?php

namespace Devolon\Messente\DTOs;


use Devolon\Common\Bases\DTO;

class MessenteConfigDTO extends DTO
{
    public function __construct(
        public string $url,
        public string $username,
        public string $password,
        public string $from,
    ) {
    }
}
