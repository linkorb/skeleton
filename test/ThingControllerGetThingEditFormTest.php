<?php

namespace LinkORB\Skeleton\Test;

use LinkORB\Skeleton\Controller\ThingController;
use LinkORB\Skeleton\Model\Thing;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Route;
use TypeError;

class ThingControllerGetThingEditFormTest extends TestCase
{
    public function testMissingThingFormThrowsWhileRenderingTheFormView(): void
    {
        $repo = new ThingEditFormRepository(null, true);
        $error = $this->renderFailure(function () use ($repo) {
            $this->controller()->addAction(
                $this->app($repo),
                Request::create('/things/add', 'GET', array('error' => 'Failed adding thing'))
            );
        });

        $this->assertSame($this->formViewFailure(), $error->getMessage());
        $this->assertNull($repo->added);
        $this->assertNull($repo->updated);
    }

    public function testExistingThingFormThrowsBeforeChangingTheStoredEmail(): void
    {
        $thing = $this->thing(15, 'Ada', 'Old@Example.COM', 'A note');
        $repo = new ThingEditFormRepository($thing, true);
        $error = $this->renderFailure(function () use ($repo) {
            $this->controller()->editAction(
                $this->app($repo),
                Request::create('/things/15/edit', 'GET'),
                15
            );
        });

        $this->assertSame($this->formViewFailure(), $error->getMessage());
        $this->assertSame('Ada', $thing->getName());
        $this->assertSame('Old@Example.COM', $thing->getEmail());
        $this->assertSame('A note', $thing->getDescription());
        $this->assertNull($repo->added);
        $this->assertNull($repo->updated);
    }

    public function testRejectedAddRedirectsWithFailedAddingThing(): void
    {
        $repo = new ThingEditFormRepository(null, false);
        $app = $this->app($repo);
        $response = $this->controller()->addAction($app, $this->submission($app, '/things/add', array(
            'name' => 'Ada',
            'email' => 'Ada@Example.COM',
            'description' => 'A note',
        )));

        $this->assertSame(302, $response->getStatusCode(), $response->getContent());
        $this->assertSame(
            '/things/add?error=Failed+adding+thing',
            $response->headers->get('Location')
        );
        $this->assertSame('ada@example.com', $repo->added->getEmail());
        $this->assertSame('Ada', $repo->added->getName());
        $this->assertSame('A note', $repo->added->getDescription());
        $this->assertNull($repo->updated);
    }

    public function testAcceptedAddStoresLowercasedEmailAndRedirectsToTheIndex(): void
    {
        $repo = new ThingEditFormRepository(null, true);
        $app = $this->app($repo);
        $response = $this->controller()->addAction($app, $this->submission($app, '/things/add', array(
            'name' => 'Ada',
            'email' => 'Ada@Example.COM',
            'description' => 'A note',
        )));

        $this->assertSame(302, $response->getStatusCode(), $response->getContent());
        $this->assertSame('/things', $response->headers->get('Location'));
        $this->assertInstanceOf(Thing::class, $repo->added);
        $this->assertSame('ada@example.com', $repo->added->getEmail());
        $this->assertSame('Ada', $repo->added->getName());
        $this->assertSame('A note', $repo->added->getDescription());
        $this->assertNull($repo->updated);
    }

    public function testEditUpdatesTheSameThingAndRedirectsToTheIndex(): void
    {
        $thing = $this->thing(15, 'Ada', 'Old@Example.COM', 'A note');
        $repo = new ThingEditFormRepository($thing, true);
        $app = $this->app($repo);
        $response = $this->controller()->editAction(
            $app,
            $this->submission($app, '/things/15/edit', array(
                'name' => 'Ada Lovelace',
                'email' => 'ADA@EXAMPLE.COM',
                'description' => 'Updated',
            )),
            15
        );

        $this->assertSame(302, $response->getStatusCode(), $response->getContent());
        $this->assertSame('/things', $response->headers->get('Location'));
        $this->assertNull($repo->added);
        $this->assertSame($thing, $repo->updated);
        $this->assertSame(15, $thing->getId());
        $this->assertSame('Ada Lovelace', $thing->getName());
        $this->assertSame('ada@example.com', $thing->getEmail());
        $this->assertSame('Updated', $thing->getDescription());
    }

    private function controller(): ThingController
    {
        return new ThingController();
    }

    private function thing(int $id, string $name, string $email, string $description): Thing
    {
        $thing = new Thing();
        $thing->setId($id);
        $thing->setName($name);
        $thing->setEmail($email);
        $thing->setDescription($description);

        return $thing;
    }

    private function app(ThingEditFormRepository $repo): Application
    {
        $app = new class($repo) extends Application {
            public function __construct(private ThingEditFormRepository $repo)
            {
                parent::__construct();
            }

            public function getThingRepository(): ThingEditFormRepository
            {
                return $this->repo;
            }
        };

        $app->register(new TranslationServiceProvider(), array('locale' => 'en'));
        $app->register(new FormServiceProvider());
        $app->register(new TwigServiceProvider(), array(
            'twig.path' => array(dirname(__DIR__) . '/templates'),
        ));
        $app['twig.loader.filesystem']->addPath(dirname(__DIR__) . '/themes/default', 'Theme');
        $app['session'] = new Session(new MockArraySessionStorage());

        $loader = new YamlFileLoader(new FileLocator(array(dirname(__DIR__) . '/app/config')));
        $app['routes']->addCollection($loader->load('routes.yml'));
        $app['routes']->add('logout', new Route('/logout'));

        return $app;
    }

    private function submission(Application $app, string $path, array $fields): Request
    {
        $fields['_token'] = (string) $app['form.csrf_provider']->getToken('form');

        return Request::create($path, 'POST', array('form' => $fields));
    }

    private function renderFailure(callable $call): TypeError
    {
        $level = ob_get_level();

        try {
            $call();
        } catch (TypeError $error) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            return $error;
        }

        $this->fail('Expected the edit form render to throw');
    }

    private function formViewFailure(): string
    {
        return 'array_key_exists(): Argument #2 ($array) must be of type array, Symfony\Component\Form\FormView given';
    }
}

class ThingEditFormRepository
{
    public ?Thing $added = null;

    public ?Thing $updated = null;

    public function __construct(private ?Thing $thing, private bool $acceptAdd)
    {
    }

    public function getById($id)
    {
        if ($this->thing === null || $this->thing->getId() !== $id) {
            return null;
        }

        return $this->thing;
    }

    public function add(Thing $thing)
    {
        $this->added = $thing;

        return $this->acceptAdd;
    }

    public function update(Thing $thing)
    {
        $this->updated = $thing;

        return $thing;
    }
}
