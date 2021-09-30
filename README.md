# Domain Checker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/masnathan/domain-api-client.svg?style=flat-square)](https://packagist.org/packages/masnathan/domain-api-client)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/masnathan/domain-api-client/run-tests?label=tests)](https://github.com/masnathan/domain-api-client/actions?query=workflow%3ATests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/masnathan/domain-api-client/Check%20&%20fix%20styling?label=code%20style)](https://github.com/masnathan/domain-api-client/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/masnathan/domain-api-client.svg?style=flat-square)](https://packagist.org/packages/masnathan/domain-api-client)

This is an easy way to check if a domain is valid, if it’s available for purchase, it's age or it's DNS settings

Request your API key [here](https://rapidapi.com/MASNathan/api/domain-checker7/)

## Installation

You can install the package via composer:

```bash
composer require masnathan/domain-api-client
```

## Usage

### Whois

```php
use MASNathan\DomainAPI\Domain;

$domainClient = new Domain('domain-checker7.p.rapidapi.com', 'super-secret-api-key');

$details = $domainClient->whois('github.com');

var_dump($details);
// array:10 [
//   "domain" => "github.com"
//   "sld" => "github"
//   "tld" => "com"
//   "valid" => true
//   "available" => false
//   "created_at" => "2007-10-09 18:20:50"
//   "updated_at" => "2020-09-08 09:18:27"
//   "expires_at" => "2022-10-09 18:20:50"
//   "registrar" => "MarkMonitor, Inc."
//   "whois" => "whois.markmonitor.com"
// ]

// you can also use the batch method to request up to 100 domains

$details = $domainClient->whoisBatch(['github.com', 'rapidapi.com', 'google.com']);
```

### DNS

```php
use MASNathan\DomainAPI\Domain;

$domainClient = new Domain('domain-checker7.p.rapidapi.com', 'super-secret-api-key');

$details = $domainClient->dns('github.com');

var_dump($details);
// array:8 [
//   "domain" => "github.com"
//   "valid" => true
//   "A" => array:1 [
//     0 => "140.82.121.4"
//   ]
//   "AAAA" => []
//   "CNAME" => array:1 [...]
//   "NS" => array:8 [...]
//   "MX" => array:5 [...]
//   "TXT" => array:8 [...]
// ]

// you can also use the batch method to request up to 100 domains

$details = $domainClient->dnsBatch(['github.com', 'rapidapi.com', 'google.com']);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [André Filipe](https://github.com/masnathan)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
