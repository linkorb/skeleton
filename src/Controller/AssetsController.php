<?php

namespace LinkORB\Skeleton\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Less_Parser;
use RuntimeException;

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
    
    public function serveAction(Application $app, Request $request, $postfix)
    {
        // Thanks StackOverflow!
        // http://stackoverflow.com/questions/2668854/sanitizing-strings-to-make-them-url-and-filename-safe
        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~,;:[]().
        $postfix = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).\/])", '', $postfix);
        // Remove any runs of periods (thanks falstro!)
        $postfix = preg_replace("([\.]{2,})", '', $postfix);
        
        $basePath = __DIR__ . '/../../assets/';
        $filename = $basePath . $postfix;
        if (!file_exists($filename)) {
            throw new RuntimeException("File not found: " . $filename);
        }
        $options = array();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        switch ($extension) {
            case 'png':
                $options['Content-Type'] = 'image/png';
                break;
            case 'jpg':
                $options['Content-Type'] = 'image/jpg';
                break;
            case 'css':
                $options['Content-Type'] = 'text/css';
                break;
            case 'js':
                $options['Content-Type'] = 'application/javascript';
                break;
            default:
                $options['Content-Type'] = 'application/octet-stream';
                $options['Content-Disposition'] = 'attachment;filename="' . basename($filename) . '"';
                break;
        }
        
        $data = file_get_contents($filename);
        return new Response($data, 200, $options);
    }
}
