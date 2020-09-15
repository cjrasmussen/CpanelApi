# CpanelApi

Simple class for making requests to the WHM/cPanel API.  Not affiliated with cPanel.


## Usage

```php
use cjrasmussen\CpanelApi\CpanelApi;

$cpanel = new CpanelApi($host, $username, $token, CpanelApi::API_UAPI);

// LIST DOMAINS
$response = $cpanel->request('DomainInfo', 'list_domains');
var_dump($response);

// ADD A SUBDOMAIN TO A DOMAIN
$subdomain = [
	'domain' => 'bar',
	'rootdomain' => 'foo.com',
	'dir' => 'bar',
];
$cpanel->request('SubDomain', 'addsubdomain', $subdomain);
```

## Installation

Simply add a dependency on cjrasmussen/cpanel-api to your composer.json file if you use [Composer](https://getcomposer.org/) to manage the dependencies of your project:

```sh
composer require cjrasmussen/cpanel-api
```

Although it's recommended to use Composer, you can actually include the file(s) any way you want.


## License

CpanelApi is [MIT](http://opensource.org/licenses/MIT) licensed.