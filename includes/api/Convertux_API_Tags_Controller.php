<?php

require_once 'Convertux_API_Abstract_Controller.php';

class Convertux_API_Tags_Controller extends Convertux_API_Abstract_Controller
{
    protected $resource_name = 'tags';

    public function registerRoutes()
    {
        register_rest_route($this->namespace, '/'.$this->resource_name, [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'getTags'],
                'permission_callback' => [$this, 'checkPermissions'],
            ],
        ]);
    }

    public function getTags($request)
    {
        return rest_ensure_response(get_tags(['get' => 'all']));
    }
}
