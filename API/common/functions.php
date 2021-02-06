<?php
require_once(dirname(__FILE__) . '/jwt.php');
$cookie_options_httponly = array(
    'expires' => time() + 1800,
    'path' => explode("/API", substr(str_replace('\\', '/',  __DIR__ . "/"), str_replace('\\', '/', strlen($_SERVER['DOCUMENT_ROOT']))))[0] . "/",
    'httponly' => true,    // or false
    /*'domain' => '.example.com', // leading dot for compatibility or use subdomain
    'secure' => true,     // or false*/
    'samesite' => 'Lax' // None || Lax  || Strict
);
$cookie_options = array(
    'expires' => time() + 1800,
    'path' => explode("/API", substr(str_replace('\\', '/',  __DIR__ . "/"), str_replace('\\', '/', strlen($_SERVER['DOCUMENT_ROOT']))))[0] . "/",
    'httponly' => false,    // or false
    /*'domain' => '.example.com', // leading dot for compatibility or use subdomain
    'secure' => true,     // or false*/
    'samesite' => 'Lax' // None || Lax  || Strict
);
function analyzeError($message)
{
    $error = array();
    $error['code'] = 0;
    $error['message'] = $message;
    if (strpos($message, 'Undefined index') !== false) {
        $error['code'] = 400;
        $error['message'] = "Bad Request";
    } else if (strpos($message, 'Duplicate') !== false) {
        $error['code'] = 409;
        $error['message'] = "Duplicate";
    }

    return $error;
}
set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    $error = analyzeError($errstr);

    throw new ErrorException($error['message'], $error['code'], $errno, $errfile, $errline);
});

function clearCookie()
{
    foreach ($_COOKIE as $name => $value) {
        setcookie($name, null, time() - 1800, explode("/API", substr(str_replace('\\', '/', __DIR__ . "/"), str_replace('\\', '/', strlen($_SERVER['DOCUMENT_ROOT']))))[0] . "/");
    }
}

function setHeader($code)
{

    if ($code === 400)
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    else if ($code === 401) {
        clearCookie();
        header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
    } else if ($code === 404) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    } else if ($code === 409) {
        header($_SERVER["SERVER_PROTOCOL"] . " 409 Conflict");
    } else if ($code === 429) {
        header($_SERVER["SERVER_PROTOCOL"] . " 429 Too Many Requests");
    } else
        header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
}



class Token
{
    /**
     * mysqli
     * @var mysqli 
     */
    private  $conn;

    /**
     * JWT payload
     * @var bool|array 
     */
    private $payload;

    /**
     * constructor
     * @param mysqli $conn 資料庫連線
     * @param string $token JWT token
     */
    function __construct($conn, $token)
    {
        $this->conn = $conn;
        $this->payload = JWT::verifyToken($token);
    }

    private function getStatus()
    {
        $sql = "SELECT SIGNUP_ENABLE,LOCK_UP,CHECKED FROM SN_DB WHERE  SN=:sn";
        $stmt = oci_parse($this->conn, $sql);
        oci_bind_by_name($stmt, ':sn', $this->payload['sn']);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $signup_enable = oci_result($stmt, "SIGNUP_ENABLE"); // what is this?
        $lockup = oci_result($stmt, "LOCK_UP"); // what is this?
        $checked = oci_result($stmt, "CHECKED"); // 0：未銷帳；1：已銷帳
        oci_free_statement($stmt);
        if ($checked === "0")
            return 0; //尚未銷帳
        else if ($lockup === "1")
            return 3; //報名完成，資料已鎖定
        else if ($signup_enable === "1")
            return 1; //尚未填寫報名表
        else if ($signup_enable === "0")
            return 2; //報名完成，資料尚未確認
        else
            return -1; //error
    }

    /**
     * 更新token
     * @return bool|string 最新狀態的token
     */
    public function refresh()
    {
        if ($this->payload === false)
            return false;
        $stmt = oci_parse($this->conn, "SELECT PWD FROM  SN_DB  WHERE SN=:sn ");
        oci_bind_by_name($stmt, ':sn', $this->payload['sn']);
        if (!oci_execute($stmt, OCI_DEFAULT)) //oci_execute($stmt) 
            return false;
        if (oci_fetch($stmt)) {
            $this->payload['pwd'] = hash('sha256', oci_result($stmt, "PWD"));
            $this->payload['iat'] = time();
            $this->payload['exp'] =  time() + 1800;
            $this->payload['status'] = $this->getStatus();
        } else
            return false;
        oci_free_statement($stmt);

        return JWT::getToken($this->payload);;
    }

    /**
     * 驗證token與資料庫狀態
     * @return bool|array 最新狀態的payload
     */
    public function verify()
    {
        if ($this->payload === false || !isset($_COOKIE['username'])) {
            clearCookie();
            return false;
        }

        $stmt = oci_parse($this->conn, "SELECT PWD FROM  SN_DB  WHERE SN=:sn ");
        oci_bind_by_name($stmt, ':sn', $this->payload['sn']);

        if (!oci_execute($stmt, OCI_DEFAULT) || !oci_fetch($stmt) || $this->payload['pwd'] !== hash('sha256', oci_result($stmt, "PWD"))) {
            clearCookie();
            return false;
        }
        oci_free_statement($stmt);
        $token = $this->refresh();
        return $this->payload;
    }
}
