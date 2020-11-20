<?php


class KerioOperatorApi
{
    protected $channel;
    public $token = null;
    protected $cookieFile = "/tmp/kerio-api-cookie";
    public $url = "https://iptel.uz:443/myphone/api/jsonrpc/";
    protected $application = [
        'name' => 'PROCRM_VOIP_APP',
        'vendor' => 'Kerio',
        'version' => '2.0'
    ];

    public function __construct($domain = null)
    {
        if ($domain)
            $this->url = $domain . ":443/myphone/api/jsonrpc/";

        $this->curlInit();
    }

    /**
     * Инитиализация curl
     */
    public function curlInit()
    {
        $this->channel = curl_init();
    }

    /**
     * Измененния домена
     * @param $domain
     */
    public function changeDomain($domain)
    {
        if ($domain)
            $this->url = $domain . ":443/myphone/api/jsonrpc/";
    }

    /**
     * Авторизация
     * @param $login
     * @param $password
     * @return mixed
     */
    public function login($login, $password)
    {
        $method = 'Session.login';

        $params = [
            'userName' => $login,
            'password' => $password,
            'application' => $this->application
        ];

        $response = $this->getRequestQuery($method, $params);

        if (isset($response['result']['token']))
            $this->token = $response['result']['token'];

        return $response;
    }


    /**
     * Запрос
     * @param $method
     * @param $params
     * @return mixed
     */
    public function getRequestQuery($method, $params)
    {
        $query = [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => $method,
            'params' => $params
        ];

        curl_setopt($this->channel, CURLOPT_URL, $this->url);
        curl_setopt($this->channel, CURLOPT_HTTPHEADER, ['User-Agent: php-curl']);
        curl_setopt($this->channel, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->channel, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->channel, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->channel, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($this->channel, CURLOPT_COOKIEFILE, $this->cookieFile);

        if ($this->token)
            curl_setopt($this->channel, CURLOPT_HTTPHEADER, ["Content-Type:application/json", "X-Token:" . $this->token]);

        curl_setopt($this->channel, CURLOPT_POSTFIELDS, json_encode($query));
        return json_decode(curl_exec($this->channel), true);
    }

    /**
     * Аторизация и запрос
     * @param $kerioStaff
     * @param $method
     * @param $params
     * @return mixed
     */
    public function loginAndQueryByStaff($kerioStaff, $method, $params) {
        $this->changeDomain($kerioStaff->domain);
        $this->login($kerioStaff->login, $kerioStaff->password);
        return $this->getRequestQuery($method, $params);
    }
}