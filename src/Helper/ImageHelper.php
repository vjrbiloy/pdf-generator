<?php

namespace App\Helper;

class ImageHelper
{
    public function __construct() 
	{

    }

    public function imageToBase64($path) 
	{
        $path = $path;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}