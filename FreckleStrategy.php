<?php
/**
 * Freckle strategy for Opauth
 *
 * Based on work by U-Zyn Chua (http://uzyn.com)
 *
 * More information on Opauth: http://opauth.org
 *
 * @copyright    Copyright © 2016 Timm Stokke (http://timm.stokke.me)
 * @link         http://opauth.org
 * @package      Opauth.FreckleStrategy
 * @license      MIT License
 */


/**
 * Freckle strategy for Opauth
 *
 * @package			Opauth.Freckle
 */
class FreckleStrategy extends OpauthStrategy {

	/**
	 * Compulsory config keys, listed as unassociative arrays
	 */
	public $expects = array('client_id', 'client_secret');

	/**
	 * Optional config keys, without predefining any default values.
	 */
	public $optionals = array('state');

	/**
	 * Optional config keys with respective default values, listed as associative arrays
	 * eg. array('scope' => 'post');
	 */
	public $defaults = array(
		'redirect_uri' => '{complete_url_to_strategy}oauth2callback'
	);

	/**
	 * Auth request
	 */
	public function request() {
		$url = 'https://secure.letsfreckle.com/oauth/2/authorize';
		$params = array(
			'response_type' => 'code',
			'client_id' => $this->strategy['client_id'],
			'redirect_uri' => $this->strategy['redirect_uri']
		);

		foreach ($this->optionals as $key) {
			if (!empty($this->strategy[$key])) $params[$key] = $this->strategy[$key];
		}

		$this->clientGet($url, $params);
	}

	/**
	 * Internal callback, after OAuth
	 */
	public function oauth2callback() {
		if (array_key_exists('code', $_GET) && !empty($_GET['code'])) {

			$code = $_GET['code'];
			$url = 'https://secure.letsfreckle.com/oauth/2/access_token';

			$params = array(
				'code' => $code,
				'client_id' => $this->strategy['client_id'],
				'client_secret' => $this->strategy['client_secret'],
				'grant_type' => 'authorization_code',
				'redirect_uri' => $this->strategy['redirect_uri'],
			);

			$response = $this->serverPost($url, $params, array(), $headers);
			parse_str($response, $results);

			if (!empty($results) && !empty($results['access_token'])) {

				$user = $this->user($results['access_token']);

				$this->auth = array(
					'uid' => $user['id'],
					'info' => array(
						'first_name' => $user['first_name'],
						'last_name' => $user['last_name'],
						'name' => $user['first_name'].' '.$user['last_name'],
						'email' => $user['email'],
						'image' => $user['profile_image_url']
					),
					'credentials' => array(
						'token' => $results['access_token'],
						'refresh_token' => $results['refresh_token'],
						'expires_in' => $results['expires_in'],
					),
					'raw' => $user
				);

				$this->callback();
			}
			else {
				$error = array(
					'code' => 'access_token_error',
					'message' => 'Failed when attempting to obtain access token',
					'raw' => array(
						'response' => $response,
						'headers' => $headers
					)
				);

				$this->errorCallback($error);
			}
		}
		else {
			$error = array(
				'code' => 'oauth2callback_error',
				'raw' => $_GET
			);

			$this->errorCallback($error);
		}
	}

	/**
	 * Queries Freckle API for user info
	 *
	 * @param string $access_token
	 * @return array Parsed JSON results
	 */

	private function user($access_token) {

		$options['http']['header'] = "Content-Type: application/json";
		$options['http']['header'] .= "\r\nAccept: application/json";

		$accountDetails = $this->serverGet('https://api.letsfreckle.com/v2/current_user/', array('access_token' => $access_token), $options, $headers);

		if (!empty($accountDetails)) {
			return $this->recursiveGetObjectVars(json_decode($accountDetails,true));
		} else {
			$error = array(
				'code' => 'userinfo_error',
				'message' => 'Failed when attempting to query Freckle API for user information',
				'raw' => array(
					'response' => $accountDetails,
					'headers' => $headers
				)
			);

			$this->errorCallback($error);
		}
	}

}
