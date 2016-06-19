<?php

/*
 * This file is part of drdelay/steam-login.
 *
 * (c) DrDelay <info@vi0lation.de>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

/**
 * @author DrDelay <info@vi0lation.de>
 */

namespace DrDelay\SteamLogin;

use GuzzleHttp\Client;

class SteamLogin
{
    const STEAMCOMM_WEBSITE = 'https://steamcommunity.com';

    /**
     * @param string      $username
     * @param string      $password
     * @param string      $otp       The token from e.G. the app
     * @param string|null $useragent A custom User-Agent
     * @param Client|null $client    The Guzzle client used for the operation
     *
     * @return string
     *
     * @throws SteamLoginException In case of errors
     */
    public static function getSteamLoginSecure($username, $password, $otp, $useragent = null, Client &$client = null)
    {
        $clientConf = [
            'cookies' => true,
        ];
        if ($useragent) {
            $clientConf['headers'] = [
                'User-Agent' => $useragent,
            ];
        }
        $client = new Client($clientConf);

        $steamRSA = new SteamRSAKey($username, $client);

        $encryptedPass = $steamRSA->encrypt($password);
        if (strlen($encryptedPass) < 32) {
            throw new SteamLoginException('Error encrypting password (encrypted string too short)');
        }

        // The real login, now that we have the password encrypted with the publickey from Steam
        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $client->post(self::STEAMCOMM_WEBSITE.'/login/dologin/', array(
            'form_params' => [
                'captcha_text' => '',
                'captchagid' => -1,
                'donotcache' => Utils::microtime_ms(true),
                'emailauth' => '',
                'emailsteamid' => '',
                'loginfriendlyname' => '',
                'password' => $encryptedPass,
                'remember_login' => 'true',
                'rsatimestamp' => $steamRSA->getSteamTimestamp(),
                'twofactorcode' => $otp,
                'username' => $username,
            ],
        ));
        Utils::jsonBody($response); // Only for the validation of success

        return Utils::getCookie($client, 'steamLoginSecure');
    }
}
