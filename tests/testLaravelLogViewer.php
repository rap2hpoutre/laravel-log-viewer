<?php
namespace Rap2hpoutre\LaravelLogViewer;

require __DIR__ . '/../vendor/autoload.php';
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class testLaravelLogViewer extends OrchestraTestCase
{

    public function setUp()
    {
        parent::setUp();
        // Copy Test laravel.log file to the orchestra package emulating laravel environment
        if (!file_exists(storage_path() . '/logs/laravel.log')) {
            copy(__DIR__ . '/laravel.log', storage_path() . '/logs/laravel.log');
        }
    }

    /**
     * @throws \Exception
     */
    public function testSetFile()
    {
        parent::setUp();

        $laravel_log_viewer = new LaravelLogViewer();
        try {
            $laravel_log_viewer->setFile("laravel.log");
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $this->assertEquals("laravel.log", $laravel_log_viewer->getFileName());
    }

    public function testAll()
    {
        $laravel_log_viewer = new LaravelLogViewer();
        $data = $laravel_log_viewer->all();
        $this->assertEquals('local', $data[0]['context']);
        $this->assertEquals('error', $data[0]['level']);
        $this->assertEquals('danger', $data[0]['level_class']);
        $this->assertEquals('exclamation-triangle', $data[0]['level_img']);
        $this->assertEquals('2018-09-05 20:20:51', $data[0]['date']);
    }

    public function testGetFolderFiles()
    {
        $laravel_log_viewer = new LaravelLogViewer();
        $data = $laravel_log_viewer->getFolderFiles();
        $this->assertNotEmpty($data[0], "Folder files is null");
    }

}
