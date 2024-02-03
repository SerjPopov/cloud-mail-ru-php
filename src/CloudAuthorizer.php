<?php

namespace SergPopov\CloudMailRu;

use Exception;

class CloudAuthorizer
{
    private $httpClient;

    private $user;
    private $pass;
    private $domain;
    private $build;
    private $token;
    private $xPageId;

    /**
     * CloudAuthorizer constructor.
     * @param string $user
     * @param string $domain
     * @param string $password
     */
    public function __construct(string $user, string $domain, string $password)
    {

        $this->user = $user;
        $this->pass = $password;
        $this->domain = $domain;
        $this->token = '';
        $this->xPageId = '';
        $this->build = '';

        $this->httpClient = new HttpClient();
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPass(): string
    {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->user . '@' . $this->domain;
    }

    /**
     * @return string
     */
    public function getBuild(): string
    {
        return $this->build;
    }

    /**
     * @return string
     */
    public function getXPageId(): string
    {
        return $this->xPageId;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @throws CloudMailRuException
     */
    public function login(): void
    {
        $this->loginStepOne();
        $this->loginStepTwo();
        $this->checkLogin();
    }

    /**
     * @throws CloudMailRuException
     */
    private function loginStepOne(): void
    {
        $url = 'https://auth.mail.ru/cgi-bin/auth?from=splash';
        $params = [
            'Password' => $this->pass,
            'new_auth_form' => '1',
            'Login' => $this->user,
            'FailPage' => '',
            'Domain' => $this->domain,
        ];
        $this->httpClient->requestPost($url, $params);
    }

    /**
     * @throws CloudMailRuException
     */
    private function loginStepTwo(): void
    {
        $url = 'https://cloud.mail.ru/home';
        $response = $this->httpClient->requestGet($url);
        $str = $response->getBody()->getContents();
        $this->token = self::getTokenFromText($str);
        $this->xPageId = self::getXPageIdFromText($str);
        $this->build = self::getBuildFromText($str);
    }

    /**
     * @throws CloudMailRuException
     */
    private function checkLogin(): void
    {
        if ($this->token == '' || $this->xPageId == '' || $this->build == '') {
            throw new CloudMailRuException('Authorization data is not received');
        }
    }

    /**
     * @param string $str
     * @param string $find
     * @return string
     */
    private static function getValue(string $str, string $find): string
    {
        $pattern = '/"' . $find . '":"(?<value>[\w\-\.]*)"/mu';
        $matches = [];
        try {
            $result = preg_match($pattern, $str, $matches);
        } catch (Exception $e) {
            return '';
        }

        if ($result === false) {
            return '';
        }

        if (isset($matches['value']) === false) {
            return '';
        }

        return $matches['value'];
    }

    /**
     * @param string $str
     * @return string
     */
    private static function getXPageIdFromText(string $str): string
    {
        return self::getValue($str, 'x-page-id');
    }

    /**
     * @param string $str
     * @return string
     */
    private static function getBuildFromText(string $str): string
    {
        return self::getValue($str, 'BUILD');
    }

    /**
     * @param string $str
     * @return string
     */
    private static function getTokenFromText(string $str): string
    {
        return self::getValue($str, 'csrf');
    }
}