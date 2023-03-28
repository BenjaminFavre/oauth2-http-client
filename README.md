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

## Full Symfony-specific example

Here is a full example of how to use this library inside a Symfony application.

- with custom Grant Type
- with Redis as a cache layer
- with [scoped](https://symfony.com/doc/current/http_client.html#scoping-client) HTTP Client definition
- with different URLs for the OAuth server and the API

First of all, we need to define 2 HTTP Clients: one for the OAuth server and one for the API.

```yaml
framework:
    http_client:
        scoped_clients:
            sharepoint_oauth.client:
                scope: '%env(resolve:SHAREPOINT_OAUTH_URL)%'
                headers:
                    Accept: 'application/json;odata=verbose'
                # other specific headers or settings if needed
            sharepoint_api.client:
                scope: '%env(resolve:SHAREPOINT_API_URL)%'
                headers:
                    Accept: 'application/json;odata=verbose'
                # other specific headers or settings if needed
```

Second, we need to define a custom Grant Type that will fetch the access token from the OAuth server to connect to SharePoint and will use `sharepoint_oauth.client` defined above. 

It differs from a built-in `ClientCredentialsGrantType` on purpose, to show how we can customize the authentication process:

```php
<?php

declare(strict_types=1);

namespace App\Sharepoint;

use BenjaminFavre\OAuthHttpClient\GrantType\GrantTypeInterface;
use BenjaminFavre\OAuthHttpClient\GrantType\Tokens;
use BenjaminFavre\OAuthHttpClient\GrantType\TokensExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CustomClientCredentialsGrantType implements GrantTypeInterface
{
    use TokensExtractor;

    public function __construct(
        private HttpClientInterface $client,
        private string $sharepointOauthClientId,
        private string $sharepointOauthClientSecret,
        private string $sharepointOauthUrl,
        private string $sharepointOauthResource,
    ) {
    }

    public function getTokens(): Tokens
    {
        $response = $this->client->request(Request::METHOD_POST, $this->sharepointOauthUrl, [
            'body' => http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $this->sharepointOauthClientId,
                'client_secret' => $this->sharepointOauthClientSecret,
                'resource' => $this->sharepointOauthResource,
            ]),
        ]);

        return $this->extractTokens($response);
    }
}
```

In order to pass the required parameters to the Grant Type, we need to define the service in `service.yaml` and bind parameters:

```yaml
services:
    App\Sharepoint\CustomClientCredentialsGrantType:
        bind:
            $client: '@sharepoint_oauth.client'
            string $sharepointOauthClientId: '%env(SHAREPOINT_OAUTH_CLIENT_ID)%'
            string $sharepointOauthClientSecret: '%env(SHAREPOINT_OAUTH_CLIENT_SECRET)%'
            string $sharepointOauthResource: '%env(SHAREPOINT_OAUTH_RESOURCE)%'
            string $sharepointOauthUrl: '%env(SHAREPOINT_OAUTH_URL)%'
```

Then, we need to define our cache layer instead of default `in-memory`, by adding the following service definition to `services.yaml`:

```yaml
BenjaminFavre\OAuthHttpClient\TokensCache\SymfonyTokensCacheAdapter:
    bind:
        $cache: '@cache.app'
        $cacheKey: 'sharepoint'
```

`@cache.app` is an application cache, configured in your system. In our example, this is a Redis cache:

```yaml
framework:
    cache:
        app: cache.adapter.redis
        default_redis_provider: '%env(REDIS_URL)%'
```

Finally, we can define our `OAuthHttpClient` service in `services.yaml` that uses `sharepoint_api.client` and sets configured Redis cache:

```yaml
BenjaminFavre\OAuthHttpClient\OAuthHttpClient:
    bind:
        $client: '@sharepoint_api.client'
        $grant: '@App\Sharepoint\CustomClientCredentialsGrantType'
    calls:
        - [ setCache, [ '@BenjaminFavre\OAuthHttpClient\TokensCache\SymfonyTokensCacheAdapter' ] ]
```

After this, `OAuthHttpClient` service is ready to be used in your application in any other classes:

```php
public function __construct(
    private OAuthHttpClient $sharepointClient,
) {
}
```
