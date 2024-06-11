<?php

namespace FluentFormPro\Integrations\CleverReach;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class API
{
    protected $clientId = null;

    protected $clientSecret = null;

    protected $accessToken = null;

    protected $callBackUrl = null;

    protected $settings = [];

    public function __construct($settings)
    {
        $this->clientId = $settings['client_id'];
        $this->clientSecret = $settings['client_secret'];
        $this->accessToken = $settings['access_token'];
        $this->callBackUrl = admin_url('?ff_cleverreach_auth');
        $this->settings = $settings;
    }

    public function redirectToAuthServer()
    {
        $url = 'https://rest.cleverreach.com/oauth/authorize.php?client_id=' . $this->clientId . '&grant=basic&response_type=code&redirect_uri=' . $this->callBackUrl;

        wp_redirect($url);
        exit();
    }

    public function checkForClientId()
    {
        $url = 'https://rest.cleverreach.com/oauth/authorize.php?client_id=' . $this->clientId . '&grant=basic&response_type=code&redirect_uri=' . $this->callBackUrl;
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $body = \json_decode($body, true);

        if (isset($body['error_description'])) {
            return new \WP_Error('invalid_client', $body['error_description']);
        }
    }

    public function generateAccessToken($code, $settings)
    {
        $response = wp_remote_post('https://rest.cleverreach.com/oauth/token.php', [
            'body' => [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => $this->callBackUrl,
                'code'          => $code
            ]
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $body = \json_decode($body, true);

        if (isset($body['error_description'])) {
            return new \WP_Error('invalid_client', $body['error_description']);
        }

        $settings['access_token'] = $body['access_token'];
        $settings['refresh_token'] = $body['refresh_token'];
        $settings['expire_at'] = time() + intval($body['expires_in']);
        return $settings;
    }

    protected function getApiSettings()
    {
        $apiSettings = $this->maybeRefreshToken();

        if (is_wp_error($apiSettings)) {
            return $apiSettings;
        }

        if (!$apiSettings['status'] || !$apiSettings['expire_at']) {
            return new \WP_Error('invalid', __('API key is invalid', 'fluentformpro'));
        }

        return [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'callback'      => $this->callBackUrl,
            'access_token'  => $this->accessToken,
            'refresh_token' => $apiSettings['refresh_token'],
            'expire_at'     => $apiSettings['expire_at']
        ];
    }

    protected function maybeRefreshToken()
    {
        $settings = $this->settings;
        $expireAt = $settings['expire_at'];

        if ($expireAt && $expireAt <= (time() - 30)) {
            // we have to regenerate the tokens
            $response = wp_remote_post('https://rest.cleverreach.com/oauth/token.php', [
                'body' => [
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $settings['refresh_token'],
                    'redirect_uri'  => $this->callBackUrl
                ]
            ]);

            if (is_wp_error($response)) {
                return $response;
            }

            $body = wp_remote_retrieve_body($response);
            $body = \json_decode($body, true);
            $code = wp_remote_retrieve_response_code($response);

            if ($code == 400) {
                if (isset($body['error_description'])) {
                    return new \WP_Error($code, $body['error_description']);
                }
                return new \WP_Error($code, __('Refresh token is not working', 'fluentformpro'));
            }

            $settings['access_token'] = $body['access_token'];
            $settings['refresh_token'] = $body['refresh_token'];
            $settings['expire_at'] = time() + intval($body['expires_in']);
        }

        return $settings;
    }

    public function makeRequest($url, $bodyArgs, $type = 'GET', $headers = false)
    {
        $settings = $this->getApiSettings();
        if (is_wp_error($settings)) {
            return $settings;
        }

        $headers['Content-type'] = 'application/x-www-form-urlencoded';

        $args = [
            'headers' => $headers
        ];

        if ($bodyArgs) {
            $args['body'] = $bodyArgs;
        }

        $args['method'] = $type;

        $request = wp_remote_request($url, $args);

        if (is_wp_error($request)) {
            $message = $request->get_error_message();
            return new \WP_Error(423, $message);
        }

        $body = json_decode(wp_remote_retrieve_body($request), true);

        if (!empty($body['error'])) {
            $error = 'Unknown Error';
            if (isset($body['error_description'])) {
                $error = $body['error_description'];
            } else {
                if (!empty($body['error']['message'])) {
                    $error = $body['error']['message'];
                }
            }
            return new \WP_Error(423, $error);
        }

        return $body;
    }

    public function subscribe($subscriber)
    {
        $response = $this->makeRequest(
            'https://rest.cleverreach.com/v3/groups/' . $subscriber['list_id'] . '/receivers',
            $subscriber,
            'POST',
            ['Authorization' => 'Bearer ' . $this->accessToken]
        );

        if (is_wp_error($response)) {
            $message = $response->get_error_message();
            $code = $response->get_error_code();
            return new \WP_Error($code, $message);
        }

        if (isset($response['errors'])) {
            return new \WP_Error('error', $response['errors']);
        }

        return $response;
    }
}
