# OAuth 2 decorator for the Symfony HTTP Client

A decorator for the [Symfony HTTP Client](https://symfony.com/doc/current/http_client.html) that helps you call APIs endpoints protected with OAuth 2.
It handles all the authentication protocol with the OAuth 2 server and let you focus solely on making your business API calls.

Designed to be minimalist and lightweight, it has literally no dependency at all, apart the Symfony Contracts of course; and it requires only the PHP JSON extension.

OAuth 2 is a relatively complex protocol which offers to authenticate in a wide variety of manners (called Grant Types in the OAuth jargon).
This decorator aims to provide all standard Grant Types out of the box. Too often however, the OAuth 2 server you will have to authenticate to will not follow strict OAuth 2 standard.
This is why the decorator has been designed such a way that every step of the authentication process is customizable.

## Installation

    composer require benjaminfavre/oauth2-http-client

## Usage

```php
use Symfony\Component\HttpClient\HttpClient;
use BenjaminFavre\OAuthHttpClient\OAuthHttpClient;
use BenjaminFavre\OAuthHttpClient\GrantType\ClientCredentialsGrantType;

$httpClient = HttpClient::create(); 

// Here we will use the client credentials grant type but it could be any other grant type
$grantType = new ClientCredentialsGrantType(
    $httpClient,
    'https://github.com/login/oauth/access_token', // The OAuth server token URL
    'the-client-id',
    'the-client-password'
);

$httpClient = new OAuthHttpClient($httpClient, $grantType);

// Then use $httpClient to make your API calls:
// $httpClient->request(...);
```

## How it works

Each time you make an HTTP request, the decorator will:
- Fetch an access token from cache or from the OAuth server if none is in cache;
- Modify your request to add the access token (usually in a header);
- Make the API call and return the response;
- Optionally, try again with a new access token if the first API call failed because of token expiration.

## Customization

Implement any of the following interfaces in order to customize the relevant authentication step, and pass an instance of your class via the relevant Decorator setter.

### GrantTypeInterface

A class in charge of fetching an access token from the OAuth server.
The decorator comes with four standard grant types:
- AuthorizationCodeGrantType;
- ClientCredentialsGrantType;
- PasswordGrantType;
- RefreshTokenGrantType.

### TokensCacheInterface

A class in charge of storing and fetching tokens from cache.
By default, the decorator uses MemoryTokensCache that caches the tokens in memory.

### RequestSignerInterface

A class in charge of modifying the API request in order to add the access token.
By default, the decorator uses BearerHeaderRequestSigner that adds the access token in the Authorization header.
You can use the HeaderRequestSigner to add the access token in another header, or you can implement the interface for more customization.

### ResponseCheckerInterface

A class in charge of checking if the API call failed because of a token expiration.
By default, the decorator uses StatusCode401ResponseChecker that identifies 401 response codes as the signal the access token needs to be renewed.
It can lead to false positives (401 response code can be returned for other reasons than token expiration), so you can implement the interface if your OAuth server returns exploitable fine-grained error reasons.