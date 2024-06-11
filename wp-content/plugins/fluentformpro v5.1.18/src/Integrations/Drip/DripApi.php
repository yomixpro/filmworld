<?php

namespace FluentFormPro\Integrations\Drip;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class DripApi
{
    protected $apiKey = null;
    protected $accountId = null;

    private $apiUrl = "https://api.getdrip.com/v2/";

    public function __construct($apiKey = null, $accountId = null)
    {
        $this->apiKey = $apiKey;
        $this->accountId = $accountId;
    }

    public function make_request($endpoint = '', $data = array(), $method = 'POST')
    {
        $data['api_key'] = $this->apiKey;

        $args =  array(
            'method'  => $method,
            'headers' => array(
                'content-type' => 'application/vnd.api+json',
                'Authorization' => 'Basic '.base64_encode( $this->apiKey )
            ),
        );

        if($method == 'POST') {
            $args['body'] = wp_json_encode($data);
            $response = wp_remote_post($this->apiUrl.$endpoint, $args);
        } else {
            $response = wp_remote_get($this->apiUrl.$endpoint, $args);
        }
        /* If WP_Error, die. Otherwise, return decoded JSON. */
        if (is_wp_error($response)) {
            return $response;
        }
        return json_decode($response['body'], true);
    }

    /**
     * Test the provided API credentials.
     *
     * @access public
     * @return array|\WP_Error
     */
    public function auth_test()
    {
        return $this->make_request('accounts', [], 'GET');
    }

    public function addContact($contact)
    {
        $accountId = $this->accountId;
        $contactObj = [
            'subscribers' => [$contact]
        ];
        $response = $this->make_request($accountId.'/subscribers',$contactObj, 'POST');


        if(!empty($response['subscribers'])) {
            return $response;
        }
        $message = 'API Eroror';

        if(is_wp_error($response)) {
            $message= $response->get_error_message();
        }

        return new \WP_Error('error', $message);
    }

    public function add_note( $contact_id, $email, $note ) {
        return $this->make_request([
            'action' => 'contact_add_note',
            'value' => (object) [
                'contact_id'     => $contact_id,
                'email'          => $email,
                'note'           => $note
            ],
        ], 'POST');
    }

}
