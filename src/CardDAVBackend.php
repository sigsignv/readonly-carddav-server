<?php

declare(strict_types=1);

namespace Cross;

use Sabre\CardDAV;
use Sabre\CardDAV\Backend\BackendInterface;
use Sabre\DAV\Exception\NotImplemented;

class CardDAVBackend implements BackendInterface
{
    public $dir;

    public function __construct(\Directory $rootDir)
    {
        $this->dir = $rootDir;
    }

    public function getAddressBooksForUser($principalUri)
    {
        if ($principalUri !== 'principals/public') {
            return [];
        }

        $addressBooks = [];
        $rootDir = new \DirectoryIterator($this->dir->path);
        foreach ($rootDir as $entry) {
            if ($entry->isDot() || !$entry->isDir()) {
                continue;
            }
            $addressBooks[] = [
                'id' => $entry->getInode(),
                'uri' => $entry->getFilename(),
                'principaluri' => 'principals/public',
                '{DAV:}displayname' => $entry->getFilename(),
                '{'.CardDAV\Plugin::NS_CARDDAV.'}addressbook-description' => '',
                '{http://calendarserver.org/ns/}getctag' => $entry->getMTime(),
            ];
        }

        return $addressBooks;
    }

    public function updateAddressBook($addressBookId, \Sabre\DAV\PropPatch $propPatch)
    {
        // Update always failed
        $propPatch->handle([], function () {
            return false;
        });
    }

    public function createAddressBook($principalUri, $url, array $properties)
    {
        throw new NotImplemented('Do not implement createAddressBook()');
    }

    public function deleteAddressBook($addressBookId)
    {
        throw new NotImplemented('Do not implement deleteAddressBook()');
    }

    public function getCards($addressbookId)
    {
        $addressBook = $this->getAddressBookDir($addressbookId);
        if (!$addressBook) {
            return false;
        }

        $addressBookDir = new \DirectoryIterator($addressBook->path);
        $cards = [];
        foreach ($addressBookDir as $entry) {
            if ($entry->isDot() || !$entry->isFile() || $entry->getExtension() !== 'vcf') {
                continue;
            }
            $cards[] = [
                'id' => $entry->getInode(),
                'uri' => $entry->getBasename('.vcf'),
                'lastmodified' => $entry->getMTime(),
                'etag' => $this->getEtag($entry->getSize(), $entry->getMTime()),
                'size' => $entry->getSize(),
            ];
        }

        return $cards;
    }

    public function getCard($addressBookId, $cardUri)
    {
        $addressBook = $this->getAddressBookDir($addressBookId);
        if (!$addressBook) {
            return false;
        }

        $path = $addressBook->path . "/{$cardUri}.vcf";
        $vcf = new \SplFileInfo($path);
        if (!$vcf->isFile()) {
            return false;
        }

        $data = \file_get_contents($path);
        if ($data === false) {
            return false;
        }

        return [
            'id' => $vcf->getInode(),
            'uri' => $vcf->getBasename('.vcf'),
            'carddata' => $data,
            'lastmodified' => $vcf->getMTime(),
            'etag' => $this->getEtag($vcf->getSize(), $vcf->getMtime()),
            'size' => $vcf->getSize(),
        ];
    }

    public function getMultipleCards($addressBookId, array $uris)
    {
        throw new NotImplemented('Can not getMultipleCards');
    }

    public function createCard($addressBookId, $cardUri, $cardData)
    {
        throw new NotImplemented('Do not implement createCard()');
    }

    public function updateCard($addressBookId, $cardUri, $cardData)
    {
        throw new NotImplemented('Do not implement updateCard()');
    }

    public function deleteCard($addressBookId, $cardUri)
    {
        throw new NotImplemented('Do not implement deleteCard()');
    }

    protected function getAddressBookDir($addressBookId) {
        $rootDir = new \DirectoryIterator($this->dir->path);
        foreach ($rootDir as $entry) {
            if ($entry->isDot() || !$entry->isDir()) {
                continue;
            }
            if ($entry->getInode() === $addressBookId) {
                return dir($entry->getPathname());
            }
        }

        return false;
    }

    protected function getEtag(int $size, int $mtime) {
        return \sprintf('"%x-%x"', $size, $mtime);
    }
}
