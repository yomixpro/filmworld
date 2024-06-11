<?php

namespace FluentFormPro\Integrations\SendFox;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class API
{
    protected $apiUrl = 'https://api.sendfox.com/';

    protected $apiKey = null;

    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    public function default_options()
    {
        return array(
            'apikey' => $this->apiKey
        );
    }

    public function make_request($action, $options = array(), $method = 'GET')
    {
        /* Build request options string. */
        $request_options = $this->default_options();

        $request_options = wp_parse_args($options, $request_options);

        $options_string = http_build_query($request_options);

        /* Build request URL. */
        $request_url = $this->apiUrl . $action;

        /* Execute request based on method. */
        switch ($method) {
            case 'POST':

                $request_url = $this->apiUrl.$action;
                $args = [];
                $args['body'] = json_encode($options);
                $args['method'] = 'POST';
                $args['headers'] = [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                    'Authorization' => 'Bearer '.$this->apiKey
                ];
                $response = wp_remote_post($request_url, $args);
                break;

            case 'GET':
                $args['headers'] = [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                    'Authorization' => 'Bearer '.$this->apiKey
                ];
                $response = wp_remote_get($request_url, $args);
                break;
        }

        /* If WP_Error, die. Otherwise, return decoded JSON. */
        if (is_wp_error($response)) {
            return [
                'error'   => 'API_Error',
                'message' => $response->get_error_message()
            ];
        } else if($response['response']['code'] == 200) {
            return json_decode($response['body'], true);
        } else {
            return [
                'error'   => 'API_Error',
                'message' => $response['response']['message']
            ];
        }
    }

    /**
     * Test the provided API credentials.
     *
     * @access public
     * @return bool
     */
    public function auth_test()
    {
        return $this->make_request('me',[], 'GET');
    }


    public function subscribe($data)
    {
        $request = $this->make_request('contacts', $data, 'POST');
        if(!empty($request['id'])) {
            return true;
        }
        return false;
    }

    /**
     * Get all Forms in the system.
     *
     * @access public
     * @return array
     */
    public function getLists()
    {
        $response = $this->make_request('lists', [], 'GET');
        if (!empty($response['error'])) {
            return [];
        }
        $list = $response['data'];
        //update to fetch all paginated list
        while($response['next_page_url'] != null){
            $nextPage = $response['current_page'] + 1;
            $response = $this->make_request('lists?page='.$nextPage, [], 'GET');
            if (!empty($response['error'])) {
                return $list;
            }
            if(!empty($response['data'])) {
                $list = array_merge ($list,$response['data']);
            }
        }
        return $list;
    }

    /**
     * Get single Form in the system.
     *
     * @access public
     * @return array
     */
    public function getList($listId)
    {
        $response = $this->make_request('lists/' . $listId . '/details.json', [
            'WithStatistics' => false
        ], 'GET');

        if (empty($response['Error'])) {
            return $response['Context'];
        }
        return false;
    }

}
