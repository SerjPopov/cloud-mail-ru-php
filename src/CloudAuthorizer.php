<?php


namespace SergPopov\CloudMailRu;


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
     * @param string $password
     */
    public function __construct($user, $password)
    {

        $this->user = $user;
        $this->pass = $password;
        $this->domain = 'mail.ru';
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
    public function login()
    {
        $this->loginStepOne();
        $this->loginStepTwo();
        $this->checkLogin();
    }

    /**
     * @throws CloudMailRuException
     */
    private function loginStepOne()
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
    private function loginStepTwo()
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
    private function checkLogin()
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
    private static function getValue(string $str, string $find)
    {
        $start = strpos($str, '"' . $find . '"');
        if ($start > 0) {
            $end = strpos($str, PHP_EOL, $start);
            $strValue = substr($str, $start, $end - $start);
            $valuerArr = explode(':', $strValue);
            $value = $valuerArr[1] ?? '';
            $value = trim($value);
            $value = trim($value, ',"');
            return $value;
        } else {
            return '';
        }
    }

    /**
     * @param string $str
     * @return string
     */
    private static function getXPageIdFromText(string $str)
    {
        return self::getValue($str, 'x-page-id');
    }

    /**
     * @param string $str
     * @return string
     */
    private static function getBuildFromText(string $str)
    {
        return self::getValue($str, 'BUILD');
    }

    /**
     * @param string $str
     * @return string
     */
    private static function getTokenFromText(string $str)
    {
        return self::getValue($str, 'csrf');
    }

}