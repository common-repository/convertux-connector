<?php

require_once 'Convertux_API_Abstract_Controller.php';

class Convertux_API_Categories_Controller extends Convertux_API_Abstract_Controller
{
    protected $resource_name = 'categories';

    public function registerRoutes()
    {
        register_rest_route($this->namespace, '/'.$this->resource_name, [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'getCategories'],
                'permission_callback' => [$this, 'checkPermissions'],
            ],
        ]);
    }

    public function getCategories($request)
    {
        return rest_ensure_response(get_categories());
    }
}
