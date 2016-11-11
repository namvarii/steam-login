# steam-login - Programmatic Steam login

[![Software License][ico-license]](LICENSE.md)

Performs a login on [steamcommunity.com](https://steamcommunity.com).
The Steam API offers a lot, but not everything, e.G. the possibility to send friend requests.
This library has the logic to perform a login on Steam (with the **Javascript RSA-password-encryption** Steam does) and return the content of the *steamLoginSecure* cookie, which is enough to call endpoints like `actions/AddFriendAjax`.

If Steam does bigger changes to their login process this library will most likely break.

## Install / Use

``` bash
$ composer require drdelay/steam-login
```

``` php
use DrDelay\SteamLogin\SteamLogin;
/** @var \GuzzleHttp\Cookie\SetCookie $cookie */
$cookie = SteamLogin::getSteamLoginSecure('johnny', 'secr3t', 'ABCD3', null, $client);
/** @var \GuzzleHttp\Client $client */
```

**Note: You should probably cache *steamLoginSecure* or even all the *$client-Cookies* somewhere.**

To add a friend for example you could then send:
``` php
use DrDelay\SteamLogin\SteamLogin;
use DrDelay\SteamLogin\Utils as SteamLoginUtils;
/** @var \GuzzleHttp\Client $client */
$client->get(SteamLogin::STEAMCOMM_WEBSITE); // Get a sessionid
/** @var \GuzzleHttp\Cookie\SetCookie $sessionid */
$sessionid = SteamLoginUtils::getCookie($client, 'sessionid');
$client->post(SteamLogin::STEAMCOMM_WEBSITE.'/actions/AddFriendAjax', array(
    'form_params' => [
        'accept_invite' => 0,
        'sessionID' => $sessionid->getValue(),
        'steamid' => '76000000000000000',
    ],
));
```

Whether you use the generated GuzzleHttp Client or not is completely up to you. The main purpose of this library is to return the *steamLoginSecure*-Cookie.

## Credits

- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[link-contributors]: ../../contributors
