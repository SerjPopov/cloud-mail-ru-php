<?php

namespace SergPopov\CloudMailRu;

/**
 * Class CloudFolder
 * @package SergPopov\CloudMailRu
 */
class CloudFolder
{
    private CloudAuthorizer $authorizer;

    /**
     * CloudFolder constructor.
     * @param CloudAuthorizer $authorizer
     */
    public function __construct(CloudAuthorizer $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    /**
     * @param string $pathFolderOnCloud
     * @return string
     * @throws CloudMailRuException
     */
    public function folderList(string $pathFolderOnCloud): string
    {
        $url = 'https://cloud.mail.ru/api/v2/folder?home=%2F'
            . '&sort={%22type%22%3A%22name%22%2C%22order%22%3A%22asc%22}'
            . '&offset=0'
            . '&limit=500'
            . '&home=' . $pathFolderOnCloud
            . '&api=2'
            . '&build=' . $this->authorizer->getBuild()
            . '&x-page-id=' . $this->authorizer->getXPageId()
            . '&email=' . $this->authorizer->getEmail()
            . '&x-email=' . $this->authorizer->getEmail()
            . '&_=' . time() . '810';

        $headers = [
            'X-CSRF-Token' => $this->authorizer->getToken(),
        ];

        $response = $this->authorizer->getHttpClient()->requestGet($url, $headers);
        return $this->authorizer->getHttpClient()->checkResponse($response);
    }

    /**
     * @param $pathFolderOnCloud
     * @throws CloudMailRuException
     */
    public function folderAdd($pathFolderOnCloud): void
    {
        $url = 'https://cloud.mail.ru/api/v2/folder/add';

        $postParams = [
            'api' => '2',
            'conflict' => '',
            'home' => $pathFolderOnCloud,
            'build' => $this->authorizer->getBuild(),
            'x-page-id' => $this->authorizer->getXPageId(),
            'email' => $this->authorizer->getEmail(),
            'x-email' => $this->authorizer->getEmail(),
            '_' => time() . '810',
        ];

        $headers = [
            'X-CSRF-Token' => $this->authorizer->getToken(),
        ];

        try {
            $this->authorizer->getHttpClient()->requestPost($url, $postParams, $headers);
        } catch (CloudMailRuException $e) {
            $error = $e->getMessage();
            if (!str_contains($error, '"exists"')) {
                throw new CloudMailRuException($error);
            }
        }
    }
}