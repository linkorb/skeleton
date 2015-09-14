<?php

use LinkORB\Skeleton\Application;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

$app->before(function (Request $request, Application $app) {
    // Define userbaseUrl in twig templates for login + signup links
    if (isset($app['userbaseUrl'])) {
        $app['twig']->addGlobal('userbaseUrl', $app['userbaseUrl']);
    }
    
    $token = $app['security.token_storage']->getToken();
    if ($token) {
        if ($request->getRequestUri()!='/login') {
            if ($token->getUser() == 'anon.') {
                // visitor is not authenticated
            } else {
                // visitor is authenticated
                $app['current_user'] = $token->getUser();
                $app['twig']->addGlobal('current_user', $token->getUser());
            }
        }
    }
    
    if ($request->attributes->has('accountName')) {
        $accountName = $request->attributes->get('accountName');
        $app['twig']->addGlobal('accountName', $accountName);
        $app['accountName'] = $accountName;
    }
    
});

return $app;
