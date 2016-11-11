<?php

namespace DrDelay\SteamLogin;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class Utils
{
    /**
     * Get the current unix time in milliseconds.
     *
     * @param bool|false $asString
     *
     * @return float|string
     */
    public static function microtime_ms($asString = false)
    {
        $microtime = round(microtime(true) * 1000);
        if ($asString) {
            $microtime = (string) $microtime;
        }

        return $microtime;
    }

    /**
     * Get the data from a JSON response (and validate it).
     *
     * @param ResponseInterface $response
     *
     * @return mixed
     *
     * @throws SteamLoginException
     */
    public static function jsonBody(ResponseInterface $response)
    {
        $json = json_decode($response->getBody());

        if (is_null($json)) {
            throw new SteamLoginException('Server did not return valid JSON');
        }

        if (!isset($json->success) || $json->success !== true) {
            throw new SteamLoginException('Request did not succeed - wrong login data?');
        }

        return $json;
    }

    /**
     * Split a $string by the first occurrence of $delim.
     *
     * @param string $delim
     * @param string $string
     *
     * @return string[]
     */
    public static function splitByFirst($delim, $string)
    {
        return explode($delim, $string, 2);
    }

    /**
     * Get a cookie from a GuzzleClient.
     *
     * @param Client $client
     * @param string $name
     *
     * @return \GuzzleHttp\Cookie\SetCookie
     *
     * @throws SteamLoginException If the cookie doesn't exist in the client
     */
    public static function getCookie(Client $client, $name)
    {
        /** @var \GuzzleHttp\Cookie\CookieJar $cookies */
        $cookies = $client->getConfig('cookies');
        foreach ($cookies as $cookie) {
            /** @var \GuzzleHttp\Cookie\SetCookie $cookie */
            if ($cookie->getName() == $name) {
                return $cookie;
            }
        }
        throw new SteamLoginException('Cookie not set');
    }
}
