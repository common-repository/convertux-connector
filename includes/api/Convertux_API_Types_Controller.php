<?php
require_once 'Convertux_API_Abstract_Controller.php';

class Convertux_API_Types_Controller extends Convertux_API_Abstract_Controller
{
    protected $resource_name = 'types';

    public function registerRoutes()
    {
        register_rest_route($this->namespace, '/'.$this->resource_name, [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'getTypes'],
                'permission_callback' => [$this, 'checkPermissions'],
            ],
        ]);
    }

    public function getTypes($request)
    {
        return rest_ensure_response(get_post_types([], 'objects'));
    }
}
