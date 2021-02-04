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
        return 0;
    }

    /**
     * 更新token
     * @return bool|string 最新狀態的token
     */
    public function refresh()
    {
        if ($this->payload === false)
            return false;
        $stmt = oci_parse($this->conn, "SELECT * FROM  signupdata  WHERE sn=:sn ");
        oci_bind_by_name($stmt, ':sn', $this->payload['sn']);

        if (!oci_execute($stmt)) //oci_execute($stmt) 
            return false;
        if ($row = oci_fetch_assoc($stmt)) {
            $this->payload['last_modified'] = $row['LAST_MODIFIED'];
            $this->payload['iat'] = time();
            $this->payload['exp'] =  time() + 1800;
            $this->payload['status'] = $this->getStatus();
        } else
            return false;

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
        $stmt = oci_parse($this->conn, "SELECT * FROM  signupdata  WHERE sn=:sn ");
        oci_bind_by_name($stmt, ':sn', $this->payload['sn']);

        if (!oci_execute($stmt)) {
            clearCookie();
            return false;
        }
        $row = oci_fetch_assoc($stmt);
        if (!$row || $this->payload['last_modified'] !== $row['LAST_MODIFIED']) {
            clearCookie();
            return false;
        }
        oci_free_statement($stmt);
        $token = $this->refresh();
        return $this->payload;
    }
}
