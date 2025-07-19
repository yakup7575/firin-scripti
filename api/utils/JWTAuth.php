<?php
/**
 * JWT Authentication Utility
 */

class JWTAuth {
    private static $secret;
    
    public static function init($secret) {
        self::$secret = $secret;
    }
    
    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($payload);
        
        $headerEncoded = self::base64UrlEncode($header);
        $payloadEncoded = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, self::$secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
    }
    
    public static function decode($token) {
        $parts = explode('.', $token);
        
        if (count($parts) != 3) {
            return false;
        }
        
        list($header, $payload, $signature) = $parts;
        
        $decodedHeader = json_decode(self::base64UrlDecode($header), true);
        $decodedPayload = json_decode(self::base64UrlDecode($payload), true);
        
        $expectedSignature = hash_hmac('sha256', $header . "." . $payload, self::$secret, true);
        $decodedSignature = self::base64UrlDecode($signature);
        
        if (!hash_equals($expectedSignature, $decodedSignature)) {
            return false;
        }
        
        if (isset($decodedPayload['exp']) && time() > $decodedPayload['exp']) {
            return false;
        }
        
        return $decodedPayload;
    }
    
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
?>