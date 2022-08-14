<?php

declare(strict_types=1);

namespace Cross\AuthBackend;

use Sabre\DAV\Auth\Backend\BackendInterface;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

class Anonymous implements BackendInterface
{
    protected $realm = 'Cross';

    public function check(RequestInterface $request, ResponseInterface $response)
    {
        $auth = new \Sabre\HTTP\Auth\Basic(
            $this->realm,
            $request,
            $response
        );
        $credential = $auth->getCredentials();
        if (!$credential) {
            return [false, 'Re-try needed'];
        }
        return [true, 'principals/anonymous'];
    }

    public function challenge(RequestInterface $request, ResponseInterface $response)
    {
        $auth = new \Sabre\HTTP\Auth\Basic(
            $this->realm,
            $request,
            $response,
        );
        $auth->requireLogin();
    }
}
