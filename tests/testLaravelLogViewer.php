<?php
/**
 * User: Elminson De Oleo Baez
 * Date: 8/31/2018
 * Time: 10:54 PM
 */

namespace Rap2hpoutre\LaravelLogViewer;

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class testLaravelLogViewer extends OrchestraTestCase
{

    public function setUp()
    {
        parent::setUp();
        //Copy Test laravel.log file to the orchestra package emulating laravel environment
        if (!file_exists(storage_path() . '/logs/laravel.log')) {
            copy(__DIR__ . '/laravel.log', storage_path() . '/logs/laravel.log');
            copy(__DIR__ . '/laravel.log', storage_path() . '/logs2/laravel.log');

        }
    }

    /**
     * @throws \Exception
     */
    public function testLaravelLogViewsetFile()
    {
        parent::setUp();

        $LaravelLogView = new LaravelLogViewer();
        try {
            $LaravelLogView->setFile("laravel.log");
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $this->assertEquals("laravel.log", $LaravelLogView->getFileName());
    }

    public function testLaravelLogViewGetAll()
    {
        $LaravelLogView = new LaravelLogViewer();
        $data = $LaravelLogView->all();
        $this->assertEquals('local', $data[0]['context']);
        $this->assertEquals('error', $data[0]['level']);
        $this->assertEquals('danger', $data[0]['level_class']);
        $this->assertEquals('exclamation-triangle', $data[0]['level_img']);
        $this->assertEquals('2018-08-03 17:40:23', $data[0]['date']);
    }

    public function testLaravelLogViewGetFolderFiles()
    {
        $LaravelLogView = new LaravelLogViewer();
        $data = $LaravelLogView->getFolderFiles();
        $this->assertNotEmpty($data[0], "Folder Files is Null");
    }

}