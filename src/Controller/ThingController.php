<?php

namespace LinkORB\Skeleton\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LinkORB\Skeleton\Model\Thing;

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
            'things/index.html.twig',
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
            'things/view.html.twig',
            $data
        ));
    }

    public function editAction(Application $app, Request $request, $thingId)
    {
        return $this->getThingEditForm($app, $request, $thingId);
    }

    public function addAction(Application $app, Request $request)
    {
        return $this->getThingEditForm($app, $request, null);
    }

    public function deleteAction(Application $app, Request $request, $thingId)
    {
        $repo = $app->getThingRepository();
        $thing = $repo->getById($thingId);
        $repo->delete($thing);

        return $app->redirect(
            $app['url_generator']->generate('things_index')
        );
    }

    private function getThingEditForm(Application $app, Request $request, $thingId)
    {
        $error = $request->query->get('error');
        $repo = $app->getThingRepository();
        $add = false;

        $thing = $repo->getById($thingId);

        if ($thing === null) {
            $defaults = null;
            $add = true;
        } else {
            $defaults = [
                'name' => $thing->getName(),
                'email' => $thing->getEmail(),
                'description' => $thing->getDescription(),
            ];
        }

        $form = $app['form.factory']->createBuilder('form', $defaults)
            ->add('name', 'text')
            ->add('email', 'email')
            ->add('description', 'textarea', array('required' => false))
            ->getForm();

        // handle form submission
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();

            if ($add) {
                $thing = new Thing();
            }

            $thing->setEmail(strtolower($data['email']))
                ->setName($data['name'])
                ->setDescription($data['description']);

            if ($add) {
                if (!$repo->add($thing)) {
                    return $app->redirect(
                        $app['url_generator']->generate('things_add', array('error' => 'Failed adding thing'))
                    );
                }
            } else {
                $repo->update($thing);
            }

            return $app->redirect($app['url_generator']->generate('things_index'));
        }

        return new Response($app['twig']->render(
            'things/edit.html.twig',
            [
                'form' => $form->createView(),
                'thing' => $thing,
                'error' => $error,
            ]
        ));
    }
}
