<?php

if (!function_exists('base64urlEncode')) {
    /**
     * @param string $str String.
     * @return string
     */
    function base64urlEncode(string $str): string
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }
}

if (!function_exists('base64urlEncode')) {
    /**
     * @param string $str String.
     * @return string
     */
    function base64urlEncode(string $str): string
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }
}

if (!function_exists('isJwtValid')) {
    /**
     * @param string $jwt JWT.
     * @param string|null $secret Secret.
     * @return bool
     */
    function isJwtValid(string $jwt, string|null $secret = null): bool
    {
        if (is_null($secret) === true) {
            $secret = config('jwt.secret');
        }

        // split the jwt
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        // check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
        $expiration = json_decode($payload)?->exp;
        $is_token_expired = ($expiration - time()) < 0;

        // build a signature based on the header and payload using the secret
        $base64_url_header = base64urlEncode($header);
        $base64_url_payload = base64urlEncode($payload);
        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
        $base64_url_signature = base64urlEncode($signature);

        // verify it matches the signature provided in the jwt
        $is_signature_valid = ($base64_url_signature === $signature_provided);

        if ($is_token_expired || !$is_signature_valid) {
            return false;
        } else {
            return true;
        }
    }
}


if (!function_exists('tokenData')) {
    /**
     * @param string $token Token.
     * @return bool
     */
    function isTokenValid(string $token): bool
    {
        $jwt = str_replace('Bearer ', '', $token);

        return isJwtValid($jwt);
    }
}

if (!function_exists('getJwtFromToken')) {
    /**
     * @param string $token Token.
     * @return string
     */
    function getJwtFromToken(string $token): string
    {
        return str_replace('Bearer ', '', $token);
    }
}

if (!function_exists('tokenData')) {
    /**
     * @param string $token Token.
     * @return array
     */
    function tokenData(string $token): array
    {
        $tokenParts = explode('.', getJwtFromToken($token));
        $payload = base64_decode($tokenParts[1]);

        return (array)json_decode($payload);
    }
}

if (!function_exists('isTokenValid')) {
    /**
     * @param string $token Token.
     * @return bool
     */
    function isTokenValid(string $token): bool
    {
        return isJwtValid(getJwtFromToken($token));
    }
}

if (!function_exists('generateJwt')) {
    /**
     * @param array $data Data.
     * @return string
     */
    function generateJwt(array $data): string
    {
        $headers = [
            'alg' => 256,
            "typ" => "jwt",
        ];

        $headers_encoded = base64urlEncode(json_encode($headers));

        $payload = [
            'exp' => time() + 60 * 60 * 3,  // TODO
            'iss' => url(),
        ];
        $payload = array_merge($payload, $data);

        $payload_encoded = base64urlEncode(json_encode($payload));

        $secret = config('jwt.secret');

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = base64urlEncode($signature);

        return "$headers_encoded.$payload_encoded.$signature_encoded";
    }
}
