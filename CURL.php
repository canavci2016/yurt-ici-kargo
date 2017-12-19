<?php

class CURL
{
    protected $ch;
    private static $static;
    private $header = [];
    private $url;

    /*
 * ApiCurl constructor.
 * @param $ch
 */
    private function __construct()
    {
        $this->ch = curl_init();
    }

    private function __clone()
    {
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param array $header
     */
    public function setHeader($header, $value = null)
    {
        if (is_array($header)) {

            foreach ($header as $key => $val) {
                if (is_numeric($key))
                    $this->header[] = $val;
                else
                    $this->header[] = $key . ": " . $val;
            }


        } else {
            if (!is_null($value)) {
                $this->header[] = $header . ": " . $value;
            }
        }

        return $this;
    }


    public function refreshHeader()
    {
        $this->header = [];
    }


    public static function getInstance()
    {
        if (is_null(self::$static))
            self::$static = new static();

        return self::$static;
    }

    public function get($url, $params = [])
    {


        $query = is_array($params) ? http_build_query($params) : $params;
        $query = empty($query) ? null : '?' . $query;
        $this->setUrl($url . '?' . $query);

        return $this->execute();

    }

    public function post($url = null, $params = [])
    {
        $this->setUrl($url);

        $this->isParamsJson($params);


        print_r($params);

        die();
        return $this->execute($params, true);
    }


    public function execute($query = [], $post = false)
    {
        $query = is_array($query) ? http_build_query($query) : $query;


        $curl_opt =
            [
                CURLOPT_URL => $this->getUrl(),
                CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
                CURLOPT_REFERER => $_SERVER['HTTP_HOST'],
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $query,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => 0,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => false,

            ];

        if (!empty($this->header))
            $curl_opt[CURLOPT_HTTPHEADER] = $this->header;


        if (!$post)
            $this->getMethod($curl_opt);


        curl_setopt_array($this->ch, $curl_opt);
        $connectionResponse = curl_exec($this->ch);


        $this->isError();


        return $connectionResponse;
    }


    //headera göre json format sorgulaması yapıyoruz
    private function isParamsJson($params)
    {
        $header = array_values($this->getHeader());
        if (count($this->inArraySearch($header, 'application/json')) > 0) {
            if (is_array($params))
                throw  new  Exception('lütfen json formatından data gönderiniz. veya Header : application/json sekmesini kaldırınız');
            elseif (is_string($params) && !$this->isJson($params))
                throw  new  Exception('lütfen json formatından data gönderiniz. veya Header : application/json sekmesini kaldırınız');

        }
    }


    //curl hatası olup olmadığını kontrol eder.
    private function isError()
    {
        $err = curl_error($this->ch);

        if ($err) {
            throw  new  \Exception($err);
        }

    }


    function __destruct()
    {
        curl_close($this->ch);
    }


    private function getMethod(&$curlArray)
    {
        unset($curlArray[CURLOPT_POST]);
        unset($curlArray[CURLOPT_POSTFIELDS]);
    }

    /*
      * array deki belli string içinde arama yapar.
      * */
    private function inArraySearch(array $array, $searchword)
    {
        $matches = array_filter($array, function ($var) use ($searchword) {
            return strpos($var, $searchword) !== false;
        });

        return $matches;
    }

    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}

