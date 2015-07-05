<?php

namespace LinkORB\Skeleton\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThingController
{
    
    public function frontpageAction(Application $app, Request $request)
    {
        return new Response($app['twig']->render(
            'frontpage.html.twig'
        ));
    }
    
    public function indexAction(Application $app, Request $request)
    {
        $repo = $app->getThingRepository();
        $things = $repo->getAll();
        
        $data = array();
        $data['things'] = $things;
        return new Response($app['twig']->render(
            'index.html.twig',
            $data
        ));
    }
        
    public function viewAction(Application $app, Request $request, $thingId)
    {
        $repo = $app->getThingRepository();
        $thing = $repo->getById($thingId);
        
        $data = array();
        $data['thing'] = $thing;
        return new Response($app['twig']->render(
            'view.html.twig',
            $data
        ));
    }
}
