<?php

/**
 * PHP實作JWT
 */
class JWT
{

    //header
    private static $header = array(
        'alg' => 'HS256', //加密的方法: HMAC、SHA256、RSA 進行 Base64 編碼
        'typ' => 'JWT'  //聲明類型
    );

    //HMAC所用之密鑰
    private static $key = '';


    /**
     * 獲取jwt token
     * @param array $payload JWT payload 格式如下非必須
     * [
     * 'iss'=>'jwt_admin', //iss (Issuer) - jwt簽發者
     * 'sub'=>'jrocket@example.com', //(Subject) - jwt所面向的用戶
     * 'aud'=>'http://example.com', //(Audience) - 接收jwt的一方
     * 'iat'=>time(), //(Issued At) - jwt的簽發時間
     * 'exp'=>time()+7200, //(Expiration Time) - jwt的過期時間，這個過期時間必須要大於簽發時間
     * 'nbf'=>time()+60, //(Not Before) - 定義在什麼時間之前，該jwt都是不可用的
     * 'jti'=>md5(uniqid('JWT').time()) //(JWT ID) - jwt的唯一身份標識，主要用來作為一次性token,從而迴避重放攻擊
     * ]
     * @return bool|string
     */
    public static function getToken(array $payload)
    {
        if (is_array($payload)) {
            $base64header = self::base64UrlEncode(json_encode(self::$header, JSON_UNESCAPED_UNICODE));
            $base64payload = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE));
            $token = $base64header . '.' . $base64payload . '.' . self::signature($base64header . '.' . $base64payload, self::$key, self::$header['alg']);
            return $token;
        } else {
            return false;
        }
    }


    /**
     * 驗證token是否有效,默認驗證exp,nbf,iat時間
     * @param string $Token 需要驗證的token
     * @return bool|string
     */
    public static function verifyToken(string $Token)
    {
        $tokens = explode('.', $Token);
        if (count($tokens) != 3)
            return false;

        list($base64header, $base64payload, $sign) = $tokens;

        //獲取jwt算法
        $base64decodeheader = json_decode(self::base64UrlDecode($base64header), JSON_OBJECT_AS_ARRAY);
        if (empty($base64decodeheader['alg']))
            return false;

        //簽名驗證
        if (self::signature($base64header . '.' . $base64payload, self::$key, $base64decodeheader['alg']) !== $sign)
            return false;

        $payload = json_decode(self::base64UrlDecode($base64payload), JSON_OBJECT_AS_ARRAY);

        //簽發時間大於當前服務器時間驗證失敗
        if (isset($payload['iat']) && $payload['iat'] > time())
            return false;

        //過期時間小宇當前服務器時間驗證失敗
        if (isset($payload['exp']) && $payload['exp'] < time())
            return false;

        //該nbf時間之前不接收處理該Token
        if (isset($payload['nbf']) && $payload['nbf'] > time())
            return false;

        return $payload;
    }

    /**
     * base64UrlEncode  https://jwt.io/ 中base64UrlEncode編碼實作
     * @param string $input 需要編碼的字串
     * @return string
     */
    private static function base64UrlEncode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * base64UrlEncode https://jwt.io/ 中base64UrlEncode解碼實作
     * @param string $input 需要解碼的字串
     * @return bool|string
     */
    private static function base64UrlDecode(string $input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $addlen = 4 - $remainder;
            $input .= str_repeat('=', $addlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * HMACSHA256签名  https://jwt.io/ 中HMACSHA256簽名實作
     * @param string $input 為base64UrlEncode(header).".".base64UrlEncode(payload)
     * @param string $key
     * @param string $alg 算法方式
     * @return mixed
     */
    private static function signature(string $input, string $key, string $alg = 'HS256')
    {
        $alg_config = array(
            'HS256' => 'sha256'
        );
        return self::base64UrlEncode(hash_hmac($alg_config[$alg], $input, $key, true));
    }
}
