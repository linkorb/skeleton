<?php

namespace LinkORB\Skeleton\Test;

use LinkORB\Skeleton\Application;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use RuntimeException;
use Silex\Application as SilexApplication;

class ApplicationConfigureParametersTest extends TestCase
{
    public function testMissingDebugAndThemePathKeepDebugOffAndTheBundledTheme(): void
    {
        $app = $this->configure(array());

        $this->assertSame(false, $app['debug']);
        $this->assertSame($this->srcRelative('themes/default'), $app['themePath']);
    }

    public function testNumericDebugBecomesTrueAndARelativeThemeIsPrefixed(): void
    {
        $app = $this->configure(array(
            'debug' => 1,
            'themePath' => 'themes',
        ));

        $this->assertSame(true, $app['debug']);
        $this->assertSame($this->srcRelative('themes'), $app['themePath']);
    }

    public function testAbsoluteMissingThemePathIsRejectedUnchanged(): void
    {
        $absolute = dirname(__DIR__) . '/themes/missing-crap-theme';

        try {
            $this->configure(array('themePath' => $absolute));
            $this->fail('Expected RuntimeException');
        } catch (RuntimeException $exception) {
            $this->assertSame('themePath invalid: ' . $absolute, $exception->getMessage());
        }
    }

    private function configure(array $parameters): Application
    {
        $app = new class($parameters) extends Application {
            public function __construct(array $parameters)
            {
                SilexApplication::__construct(array('parameters' => $parameters));
            }
        };

        (new ReflectionMethod(Application::class, 'configureParameters'))->invoke($app);

        return $app;
    }

    private function srcRelative(string $path): string
    {
        return dirname(__DIR__) . '/src/../' . $path;
    }
}
