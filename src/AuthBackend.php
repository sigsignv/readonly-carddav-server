<?php

declare(strict_types=1);

namespace Cross;

use Sabre\DAV\Auth\Backend\BackendInterface;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

class AuthBackend implements BackendInterface
{
    protected $realm = 'Cross';

    public function check(RequestInterface $request, ResponseInterface $response)
    {
        $auth = new \Sabre\HTTP\Auth\Basic(
            $this->realm,
            $request,
            $response
        );
        if (!$auth->getCredentials()) {
            return [false, 'Basic auth must be failed at least once'];
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
