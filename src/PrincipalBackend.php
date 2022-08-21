<?php

declare(strict_types=1);

namespace Cross;

use Sabre\DAVACL\PrincipalBackend\AbstractBackend;

class PrincipalBackend extends AbstractBackend
{
    public function getPrincipalsByPrefix($prefixPath)
    {
        $principals = [];
        if ($prefixPath === 'principals') {
            $principals[] = [
                'uri' => 'principals/anonymous',
                '{http://sabredav.org/ns}email-address' => 'anonymous@example.com',
                '{DAV:}displayname' => 'Anonymous',
            ];
        }
        return $principals;
    }

    public function getPrincipalByPath($path)
    {
        $principal = [];
        if ($path === 'principals/anonymous') {
            $principal = [
                'id' => 1,
                'uri' => 'principals/anonymous',
                '{http://sabredav.org/ns}email-address' => 'anonymous@example.com',
                '{DAV:}displayname' => 'Anonymous',
            ];
        }
        return $principal;
    }

    public function updatePrincipal($path, \Sabre\DAV\PropPatch $propPatch)
    {
        // Update always failed
        $propPatch->handle([], function () {
            return false;
        });
    }

    public function searchPrincipals($prefixPath, array $searchProperties, $test = 'allof')
    {
        if ($prefixPath !== 'principals' || empty($searchProperties)) {
            return [];
        }
        return ['principals/anonymous'];
    }

    public function getGroupMemberSet($principal)
    {
        return [];
    }

    public function getGroupMembership($principal)
    {
        return [];
    }

    public function setGroupMemberSet($principal, array $members)
    {
        return;
    }
}
