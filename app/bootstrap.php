<?php

use LinkORBSkeleton\Application;

use Symfony\Component\HttpFoundation\Request;

$app = new Application();

$app->before(function (Request $request, Application $app) {
});

return $app;
