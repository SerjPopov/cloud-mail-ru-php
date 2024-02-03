<?php

namespace SergPopov\CloudMailRu;

/**
 * Class CloudFile
 * @package SergPopov\CloudMailRu
 */
class CloudFile
{
    private CloudAuthorizer $authorizer;

    /**
     * CloudFile constructor.
     * @param CloudAuthorizer $authorizer
     */
    public function __construct(CloudAuthorizer $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    /**
     * @param string $pathLocalFile
     * @param string $pathFileOnCloud
     * @throws CloudMailRuException
     */
    public function fileUpload(string $pathLocalFile, string $pathFileOnCloud)
    {
        $size = filesize($pathLocalFile);
        if ($size === false) {
            throw new CloudMailRuException('File not found');
        }

        $uploadUrl = $this->getUploadUrl();
        $hash = $this->putFile($uploadUrl, $pathLocalFile);

        $this->addFile($hash, $size, $pathFileOnCloud);
    }

    /**
     * @param string $hash
     * @param int $size
     * @param string $pathOnCloud
     * @throws CloudMailRuException
     */
    private function addFile(string $hash, int $size, string $pathOnCloud)
    {
        $url = 'https://cloud.mail.ru/api/v2/file/add';

        $postParams = [
            'api' => '2',
            'conflict' => 'strict',
            'home' => $pathOnCloud,
            'hash' => $hash,
            'size' => $size,
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
            if (strpos($error, '"exists"') === false) {
                throw new CloudMailRuException($error);
            }
        }
    }

    /**
     * @param string $uploadUrl URL of node for file upload. Must be get before uploading the file
     * @param string $pathLocalFile
     * @return string HASH of uploaded file
     * @throws CloudMailRuException
     */
    private function putFile(string $uploadUrl, string $pathLocalFile)
    {
        $url = $uploadUrl . '?cloud_domain=2&x-email=' . $this->authorizer->getEmail();

        $body = fopen($pathLocalFile, 'r');

        if ($body === false) {
            throw new CloudMailRuException('File not found');
        }

        $response = $this->authorizer->getHttpClient()->requestPut($url, $body);

        if ($response->getStatusCode() !== 201) {
            throw new CloudMailRuException('Error uploading file');
        }

        return $response->getBody()->getContents();
    }

    /**
     * @return string URL of node for file upload
     * @throws CloudMailRuException
     */
    private function getUploadUrl()
    {
        $url = 'https://cloud.mail.ru/api/v2/dispatcher/';

        $postParams = [
            'api' => '2',
            'build' => $this->authorizer->getBuild(),
            'x-page-id' => $this->authorizer->getXPageId(),
            'email' => $this->authorizer->getEmail(),
            'x-email' => $this->authorizer->getEmail(),
            '_' => time() . '810',
        ];

        $headers = [
            'X-CSRF-Token' => $this->authorizer->getToken(),
        ];

        $response = $this->authorizer->getHttpClient()->requestPost($url, $postParams, $headers);

        try {
            $data = json_decode($this->authorizer->getHttpClient()->checkResponse($response));
        } catch (\Exception $e) {
            throw new CloudMailRuException('Json decode upload url error');
        }

        $uploadUrl = $data->body->upload[0]->url ?? '';

        if ($uploadUrl === '') {
            throw new CloudMailRuException('Get upload url error');
        }

        return $uploadUrl;
    }

    /**
     * @param string $pathOnCloud
     * @return string
     * @throws CloudMailRuException
     */
    public function filePublish(string $pathOnCloud)
    {
        $url = 'https://cloud.mail.ru/api/v2/file/publish';

        $postParams = [
            'api' => '2',
            'conflict' => '',
            'home' => $pathOnCloud,
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
            $response = $this->authorizer->getHttpClient()->requestPost($url, $postParams, $headers);
            return 'https://cloud.mail.ru/public/' . $this->getBodyFromResponse($response->getBody()->getContents());
        } catch (CloudMailRuException $e) {
            throw new CloudMailRuException($e->getMessage());
        }
    }

    /**
     * @param string $response
     * @return string
     * @throws CloudMailRuException
     */
    private function getBodyFromResponse(string $response)
    {
        $jsonObj = json_decode($response);
        $body = $jsonObj->body ?? '';
        if ($body !== '') {
            return $body;
        } else {
            throw new CloudMailRuException('Error parsing body');
        }
    }

    /**
     * @param string $pathOnCloud
     * @throws CloudMailRuException
     */
    public function fileRemove(string $pathOnCloud)
    {
        $url = 'https://cloud.mail.ru/api/v2/file/remove';

        $postParams = [
            'api' => '2',
            'conflict' => '',
            'home' => $pathOnCloud,
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
            throw new CloudMailRuException($e->getMessage());
        }
    }
}