<?php

namespace LinkORB\Skeleton\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Less_Parser;

class AssetsController
{
    public function styleAction(Application $app, Request $request)
    {
        $filename = __DIR__ . '/../../themes/default/style.less';
        $baseUrl = '/';
        
        $parser = new Less_Parser();
        $parser->parseFile($filename, $baseUrl);
        $css = $parser->getCss();
        
        return new Response($css, 200, array('Content-Type' => 'text/css'));
    }
}
