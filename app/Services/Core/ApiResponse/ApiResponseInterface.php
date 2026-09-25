<?php

namespace App\Services\Core\ApiResponse;

interface ApiResponseInterface
{
    const CODE_PROCESSING = 102;
    const CODE_OK= 200;
    const CODE_CREATED= 201;
    const CODE_ACCEPTED= 202;
    const CODE_UNAUTHORIZED= 203;
    const CODE_NO_CONTENT= 204;
    const CODE_INVALID_DATA= 222;
    const CODE_INTERNAL_SERVER_ERROR= 250;
}
