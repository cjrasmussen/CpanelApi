<?php
namespace cjrasmussen\CpanelApi;

use RuntimeException;

class CpanelApi
{
	public const API_UAPI = 1;
	public const API_WHM = 2;

	private $user;
	private $token;
	private $host;
	private $api;

	/**
	 * CpanelApi constructor.
	 *
	 * @param string $host
	 * @param string $username
	 * @param string $token
	 * @param int|null $api
	 */
	public function __construct($host, $username, $token, $api = null)
	{
		$this->host = $host;
		$this->user = $username;
		$this->token = $token;

		if (in_array($api, [self::API_UAPI, self::API_WHM], true)) {
			$this->api = $api;
		} else {
			$this->api = self::API_WHM;
		}
	}

	/**
	 * Make a request to the cPanel/WHM API
	 *
	 * @param string $module
	 * @param string $function
	 * @param array $args
	 * @return mixed
	 */
	public function request($module, $function, array $args = [])
	{
		if (!is_array($args)) {
			$args = [$args];
		}

		$header = [];

		if ($this->api === self::API_WHM) {
			$url = 'https://' . $this->host . ':2087/json-api/cpanel?cpanel_jsonapi_user=' . urlencode($this->user) . '&cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=' . urlencode($module) . '&cpanel_jsonapi_func=' . urlencode($function);
			$header[0] = 'Authorization: WHM root:' . $this->token;
		} else {
			$url = 'https://' . $this->host . ':2083/execute/' . ucwords($module) . '/' . $function;
			$header[0] = 'Authorization: cpanel ' . $this->user . ':' . $this->token;
		}

		if (count($args)) {
			if (strpos($url, '?') !== false) {
				$url .= '&' . http_build_query($args);
			} else {
				$url .= '?' . http_build_query($args);
			}
		}

		$c = curl_init();
		curl_setopt($c, CURLOPT_HEADER, 0);
		curl_setopt($c, CURLOPT_VERBOSE, 0);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($c, CURLOPT_HTTPHEADER, $header);
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_HTTPGET, 1);
		$response = curl_exec($c);
		curl_close($c);

		// DECODE THE RESPONSE INTO A GENERIC OBJECT
		$data = json_decode($response);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new RuntimeException('API response was not valid JSON');
		}

		return $data;
	}
}
