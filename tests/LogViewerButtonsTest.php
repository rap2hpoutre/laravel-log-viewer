<?php

namespace Rap2hpoutre\LaravelLogViewer\Tests;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

/**
 * Tests for config-driven button visibility (backend enforcement).
 *
 * @package Rap2hpoutre\LaravelLogViewer
 */
class LogViewerButtonsTest extends OrchestraTestCase
{
    /**
     * @var string
     */
    private $logFile = 'buttons-test.log';

    protected function getPackageProviders($app)
    {
        return [LaravelLogViewerServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    protected function defineRoutes($router)
    {
        $router->get('logs', [LogViewerController::class, 'index']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        File::ensureDirectoryExists(storage_path('logs'));
        File::put($this->logPath(), '[2018-09-05 20:20:51] local.ERROR: Something went wrong');
    }

    protected function tearDown(): void
    {
        File::delete($this->logPath());
        parent::tearDown();
    }

    private function logPath(): string
    {
        return storage_path('logs/' . $this->logFile);
    }

    private function encrypted(): string
    {
        return Crypt::encryptString($this->logFile);
    }

    public function testDeleteRemovesFileWhenEnabled()
    {
        config()->set('logviewer.buttons.delete', true);

        $this->get('logs?del=' . $this->encrypted());

        $this->assertFileDoesNotExist($this->logPath());
    }

    public function testDeleteKeepsFileWhenDisabled()
    {
        config()->set('logviewer.buttons.delete', false);

        $this->getJson('logs?del=' . $this->encrypted());

        $this->assertFileExists($this->logPath());
    }

    public function testCleanEmptiesFileWhenEnabled()
    {
        config()->set('logviewer.buttons.clean', true);

        $this->get('logs?clean=' . $this->encrypted());

        $this->assertFileExists($this->logPath());
        $this->assertSame('', File::get($this->logPath()));
    }

    public function testCleanKeepsContentWhenDisabled()
    {
        config()->set('logviewer.buttons.clean', false);

        $this->getJson('logs?clean=' . $this->encrypted());

        $this->assertNotSame('', File::get($this->logPath()));
    }

    public function testDeleteAllRemovesFilesWhenEnabled()
    {
        config()->set('logviewer.buttons.delete_all', true);

        $this->get('logs?delall=true');

        $this->assertFileDoesNotExist($this->logPath());
    }

    public function testDeleteAllKeepsFilesWhenDisabled()
    {
        config()->set('logviewer.buttons.delete_all', false);

        $this->getJson('logs?delall=true');

        $this->assertFileExists($this->logPath());
    }
}
