<?php

namespace Gbowo\Contract\Adapter;

use GuzzleHttp\Client;

interface AdapterClientInterface
{
    public function getHttpClient(): Client;
}
