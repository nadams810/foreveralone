<?php

namespace phpoauthlib2;

// Copied from PHP manual comment section:
// http://php.net/manual/en/book.curl.php#90821
// Modified to fit Inefero's/srchub needs
// Now used for oauth lib
class ccurl {
    protected $_useragent = 'phpoauthlib2 (http://www.srchub.org); https://srchub.org/p/phpoauthlib2 PHPOAuthLib';
    protected $_url;
    protected $_followlocation;
    protected $_timeout;
    protected $_maxRedirects;
    protected $_cookieFileLocation = './cookie.txt';
    protected $_post;
    protected $_postFields;
    protected $_referer ="";

    // Get around some broken webservers *cough*IIS*cough*?
    // http://stackoverflow.com/questions/14459704/does-empty-expect-header-mean-anything
    protected $_header = array('Expect:');

    protected $_session;
    protected $_webpage;
    protected $_includeHeader;
    protected $_noBody;
    protected $_status;
    protected $_binaryTransfer;
    public    $authentication = 0;
    public    $auth_name      = '';
    public    $auth_pass      = '';

    public function useAuth($use){
        $this->authentication = 0;
        if($use == true) $this->authentication = 1;
    }

    public function setName($name){
        $this->auth_name = $name;
    }
    public function setPass($pass){
        $this->auth_pass = $pass;
    }

    public function addHeader($head)
    {
        $this->_header[] = $head;
    }

    public function __construct($url,$followlocation = true,$timeOut = 30,$maxRedirecs = 4,$binaryTransfer = false,$includeHeader = false,$noBody = false)
    {
        $this->_url = $url;
        $this->_followlocation = $followlocation;
        $this->_timeout = $timeOut;
        $this->_maxRedirects = $maxRedirecs;
        $this->_noBody = $noBody;
        $this->_includeHeader = $includeHeader;
        $this->_binaryTransfer = $binaryTransfer;

        $this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt';

    }

    public function setReferer($referer){
        $this->_referer = $referer;
    }

    public function setCookiFileLocation($path)
    {
        $this->_cookieFileLocation = $path;
    }

    public function setPost ($postFields)
    {
        $this->_post = true;
        $this->_postFields = $postFields;
    }

    public function setUserAgent($userAgent)
    {
        $this->_useragent = $userAgent;
    }

    public function createCurl($url = 'nul')
    {
        if($url != 'nul'){
            $this->_url = $url;
        }

        $s = curl_init();

        curl_setopt($s,CURLOPT_URL,$this->_url);

        // I understand the implications here - but this isn't a client application
        // if my ISP is performing MITM sniffing I have bigger fish to fry
        // also the security of a CA signed certificate is questionable at best
        // https://www.schneier.com/blog/archives/2012/02/verisign_hacked.html
        // Email me if you want to discus this adamsna@datanethost.net
        // NA - 12/10/2014
        curl_setopt($s, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($s,CURLOPT_HTTPHEADER,$this->_header);
        curl_setopt($s,CURLOPT_TIMEOUT,$this->_timeout);
        curl_setopt($s,CURLOPT_MAXREDIRS,$this->_maxRedirects);
        curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($s,CURLOPT_FOLLOWLOCATION,$this->_followlocation);
        curl_setopt($s,CURLOPT_COOKIEJAR,$this->_cookieFileLocation);
        curl_setopt($s,CURLOPT_COOKIEFILE,$this->_cookieFileLocation);

        if($this->authentication == 1){
            curl_setopt($s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass);
        }
        if($this->_post)
        {
            //curl_setopt($s,CURLOPT_POST,true);
            curl_setopt($s, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($s,CURLOPT_POSTFIELDS,$this->_postFields);

        }

        if($this->_includeHeader)
        {
            curl_setopt($s,CURLOPT_HEADER,true);
        }

        if($this->_noBody)
        {
            curl_setopt($s,CURLOPT_NOBODY,true);
        }

        curl_setopt($s,CURLOPT_USERAGENT,$this->_useragent);
        curl_setopt($s,CURLOPT_REFERER,$this->_referer);

        $this->_webpage = curl_exec($s);
        $this->_status = curl_getinfo($s,CURLINFO_HTTP_CODE);
        curl_close($s);

    }

    public function getHttpStatus()
    {
        return $this->_status;
    }

    public function __tostring(){
        return $this->_webpage;
    }
}