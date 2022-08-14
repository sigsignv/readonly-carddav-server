<?php

declare(strict_types=1);

namespace Cross\AuthBackend;

use Sabre\DAV\Auth\Backend\BackendInterface;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

class Anonymous implements BackendInterface
{
    public function check(RequestInterface $request, ResponseInterface $response)
    {
        return [true, 'principals/anonymous'];
    }

    public function challenge(RequestInterface $request, ResponseInterface $response)
    {
        $auth = new \Sabre\HTTP\Auth\Basic(
            'Cross',
            $request,
            $response,
        );
        $auth->requireLogin();
    }
}
