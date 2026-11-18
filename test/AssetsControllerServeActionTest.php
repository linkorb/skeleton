<?php

namespace LinkORB\Skeleton\Test;

use LinkORB\Skeleton\Controller\AssetsController;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AssetsControllerServeActionTest extends TestCase
{
    /** @var list<string> */
    private array $created = [];

    protected function tearDown(): void
    {
        foreach ($this->created as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    public function testMissingFileThrowsTheSanitizedPath(): void
    {
        try {
            $this->serve('../missing!.txt');
            $this->fail('Expected RuntimeException');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'File not found: ' . $this->assetsPrefix() . '/missing.txt',
                $exception->getMessage()
            );
        }
    }

    public function testPngIsServedAsImagePng(): void
    {
        $postfix = 'img/linkorbengineering-logo.png';

        $this->assertServed($postfix, 'image/png', null, $this->readAsset($postfix));
    }

    public function testJpgIsServedAsImageJpg(): void
    {
        $postfix = 'crap-serve.jpg';
        $body = 'not-a-jpeg';
        $this->writeAsset($postfix, $body);

        $this->assertServed($postfix, 'image/jpg', null, $body);
    }

    public function testCssIsServedAsTextCss(): void
    {
        $postfix = 'crap-serve.css';
        $body = 'body{}';
        $this->writeAsset($postfix, $body);

        $this->assertServed($postfix, 'text/css', null, $body);
    }

    public function testJsIsServedAsApplicationJavascript(): void
    {
        $postfix = 'js/app.js';

        $this->assertServed($postfix, 'application/javascript', null, $this->readAsset($postfix));
    }

    public function testOtherExtensionIsAnAttachment(): void
    {
        $postfix = 'crap-serve.dat';
        $body = 'plain';
        $this->writeAsset($postfix, $body);

        $this->assertServed(
            $postfix,
            'application/octet-stream',
            'attachment;filename="' . $postfix . '"',
            $body
        );
    }

    private function assertServed(string $postfix, string $contentType, ?string $disposition, string $body): void
    {
        $response = $this->serve($postfix);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($contentType, $response->headers->get('Content-Type'));
        $this->assertSame($disposition, $response->headers->get('Content-Disposition'));
        $this->assertSame($body, $response->getContent());
    }

    private function serve(string $postfix): Response
    {
        $app = new class extends Application {
            public function __construct()
            {
            }
        };

        return (new AssetsController())->serveAction($app, new Request(), $postfix);
    }

    private function assetsPrefix(): string
    {
        return dirname(__DIR__) . '/src/Controller/../../assets/';
    }

    private function readAsset(string $postfix): string
    {
        $contents = file_get_contents(dirname(__DIR__) . '/assets/' . $postfix);
        $this->assertNotFalse($contents);

        return $contents;
    }

    private function writeAsset(string $postfix, string $body): void
    {
        $path = dirname(__DIR__) . '/assets/' . $postfix;
        $this->assertNotFalse(file_put_contents($path, $body));
        $this->created[] = $path;
    }
}
