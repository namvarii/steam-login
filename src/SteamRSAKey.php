<?php

namespace DrDelay\SteamLogin;

use GuzzleHttp\Client;
use phpseclib\Crypt\RSA;
use phpseclib\Math\BigInteger;

class SteamRSAKey
{
    /** @var BigInteger */
    protected $publickey_mod;

    /** @var BigInteger */
    protected $publickey_exp;

    /** @var string */
    protected $timestamp;

    /** @var RSA */
    protected $rsa_key;

    /**
     * Constructor.
     *
     * @param string $username
     * @param Client $client
     *
     * @throws SteamLoginException In case of errors
     */
    public function __construct($username, Client $client)
    {
        $responseBody = Utils::jsonBody($client->post(SteamLogin::STEAMCOMM_WEBSITE.'/login/getrsakey/', array(
            'form_params' => [
                'donotcache' => Utils::microtime_ms(true),
                'username' => $username,
            ],
        )));

        $this->publickey_mod = new BigInteger($responseBody->publickey_mod, 16);
        $this->publickey_exp = new BigInteger($responseBody->publickey_exp, 16);
        $this->timestamp = $responseBody->timestamp;

        $this->rsa_key = new RSA();
        $this->rsa_key->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        $this->rsa_key->loadKey(array(
            'e' => $this->publickey_exp,
            'n' => $this->publickey_mod,
        ));
    }

    /**
     * Encrypt with the public key.
     *
     * @param $string
     *
     * @return string base64 encoded
     */
    public function encrypt($string)
    {
        return base64_encode($this->rsa_key->encrypt($string));
    }

    /**
     * Get the public key.
     *
     * @return RSA
     */
    public function getRSAKey()
    {
        return $this->rsa_key;
    }

    /**
     * Get the timestamp Steam sent to us.
     *
     * @return string
     */
    public function getSteamTimestamp()
    {
        return $this->timestamp;
    }
}
