<?php

abstract class Convertux_API_Abstract_Controller extends WP_REST_Controller
{
    protected $namespace = 'convertux/api';

    public function checkPermissions($request)
    {
        if ($key = $request->get_param('key') === null || $convertuxKey = get_option('convertux_key', null) === null) {
            return false;
        }

        return $key === hash('crc32b', $convertuxKey);
    }
}
