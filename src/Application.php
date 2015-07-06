<?php

namespace LinkORB\Skeleton;

use Silex\Application as SilexApplication;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SecurityServiceProvider as SilexSecurityServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Parser as YamlParser;
use LinkORB\Component\DatabaseManager\DatabaseManager;
use LinkORB\Skeleton\Repository\PdoThingRepository;
use RuntimeException;
use PDO;

class Application extends SilexApplication
{
    private $pdo;

    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->configureParameters();
        $this->configurePdo();
        $this->configureService();
        $this->configureRepositories();
        $this->configureRoutes();
        $this->configureTemplateEngine();

        // $this->configureSecurity();
    }

    private function getConfigFromParameters()
    {
        if (!$this->offsetExists('parameters')) {
            $parser = new YamlParser();
            $this['parameters'] = $parser->parse(file_get_contents(__DIR__.'/../app/config/parameters.yml'));
        }

        return $this['parameters'];
    }

    private function configureParameters()
    {
        $this['debug'] = false;
        $parameters = $this->getConfigFromParameters();
        if (isset($parameters['debug'])) {
            $this['debug'] = !!$parameters['debug'];
        }
    }

    private function configurePdo()
    {
        if (!isset($this['parameters']['pdo'])) {
            throw new RuntimeException("Missing required PDO configuration");
        }
        $url = $this['parameters']['pdo'];
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $user = parse_url($url, PHP_URL_USER);
        $pass = parse_url($url, PHP_URL_PASS);
        $host = parse_url($url, PHP_URL_HOST);
        $dbname = parse_url($url, PHP_URL_PATH);

        $dsn = $scheme . ':dbname=' . substr($dbname, 1) . ';host=' . $host;
        $this->pdo = new PDO($dsn, $user, $pass);
    }

    private function configureService()
    {
        $this->register(new RoutingServiceProvider());

        // the form service
        $this->register(new TranslationServiceProvider(), array(
              'locale' => 'en',
              'translation.class_path' =>  __DIR__.'/../vendor/symfony/src',
              'translator.messages' => array(),
        ));
        $this->register(new FormServiceProvider());
    }

    private function configureRoutes()
    {
        $locator = new FileLocator(array(__DIR__.'/../app/config'));
        $loader = new YamlFileLoader($locator);
        $this['routes'] = $loader->load('routes.yml');
    }

    private function configureTemplateEngine()
    {
        $this->register(new TwigServiceProvider(), array(
            'twig.path' => array(
                __DIR__.'/../templates/',
            ),
        ));
    }

    private function configureSecurity()
    {
        $this->register(new SilexSecurityServiceProvider(), array());

        $parameters = $this->getConfigFromParameters();
        $security = $parameters['security'];

        if (isset($security['encoder'])) {
            $digest = '\\Symfony\\Component\\Security\\Core\\Encoder\\'.$security['encoder'];
            $this['security.encoder.digest'] = new $digest(true);
        }

        $this['security.firewalls'] = array(
            'default' => array(
                'stateless' => true,
                'pattern' => '^/',
                'http' => true,
                'users' => $this->getUserSecurityProvider(),
            ),
        );
    }

    private function getUserSecurityProvider()
    {
        $parameters = $this->getConfigFromParameters();
        foreach ($parameters['security']['providers'] as $provider => $providerConfig) {
            switch ($provider) {
                // case 'JsonFile':
                // return new \LinkORB\Skeleton\Security\JsonFileUserProvider(__DIR__.'/../'.$providerConfig['path']);
                // case 'Pdo':
                //     $dbmanager = new DatabaseManager();
                //
                // return new \LinkORB\Skeleton\Security\PdoUserProvider(
                //     $dbmanager->getPdo($providerConfig['database'])
                // );
                case 'UserBase':
                    return new \UserBase\Client\UserProvider(
                        new \UserBase\Client\Client(
                            $providerConfig['url'],
                            $providerConfig['username'],
                            $providerConfig['password']
                        )
                    );
                default:
                    break;
            }
        }
        throw new RuntimeException('Cannot find any security provider');
    }

    private $thingRepository;
    private function configureRepositories()
    {
        $this->thingRepository = new PdoThingRepository($this->pdo);
    }

    public function getThingRepository()
    {
        return $this->thingRepository;
    }
}
