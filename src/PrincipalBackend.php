<?php

declare(strict_types=1);

namespace Cross;

class PrincipalBackend extends \Sabre\DAVACL\PrincipalBackend\AbstractBackend
{
    public function getPrincipalsByPrefix($prefixPath)
    {
        $principals = [];
        if ($prefixPath === 'principals') {
            $principals[] = [
                'uri' => 'principals/public',
                '{http://sabredav.org/ns}email-address' => 'anonymous@example.com',
                '{DAV:}displayname' => 'Public',
            ];
        }
        return $principals;
    }

    public function getPrincipalByPath($path)
    {
        $principal = [];
        if ($path === 'principals/public') {
            $principal = [
                'id' => 1,
                'uri' => 'principals/public',
                '{http://sabredav.org/ns}email-address' => 'anonymous@example.com',
                '{DAV:}displayname' => 'Public',
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
        if ($prefixPath !== 'principals' || \count($searchProperties) === 0) {
            return [];
        }
        return ['principals/public'];
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
