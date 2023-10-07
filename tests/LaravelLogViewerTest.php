<?php

namespace Rap2hpoutre\LaravelLogViewer;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use File;

/**
 * Class LaravelLogViewerTest
 * @package Rap2hpoutre\LaravelLogViewer
 */
class LaravelLogViewerTest extends OrchestraTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        config()->set('app.key', 'XP0aw2Dkrk22p0JoAOzulOl8XkUxlvkO');
        // Copy "laravel.log" file to the orchestra package.
        if (!file_exists(storage_path('logs/laravel.log'))) {
            copy(__DIR__ . '/laravel.log', storage_path('logs/laravel.log'));
        }
    }

    /**
     * @throws \Exception
     */
    public function testSetFile()
    {

        $laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setFile("laravel.log");

        $this->assertEquals("laravel.log", $laravel_log_viewer->getFileName());
    }


	public function testSetFolderWithCorrectPath()
	{

		$laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setFolder(basename((__DIR__)));
		$this->assertEquals("tests", $laravel_log_viewer->getFolderName());
	}


	public function testSetFolderWithArrayStoragePath()
	{
        $path = __DIR__;
        
		$laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setStoragePath([$path]);
        if(!\File::exists("$path/samuel")) \File::makeDirectory("$path/samuel");
        $laravel_log_viewer->setFolder('samuel');
        
		$this->assertEquals("samuel", $laravel_log_viewer->getFolderName());

	}

    public function testSetFolderWithDefaultStoragePath()
	{
      
		$laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setStoragePath(storage_path());
        $laravel_log_viewer->setFolder('logs');

        
		$this->assertEquals("logs", $laravel_log_viewer->getFolderName());

	}

	public function testSetStoragePath()
	{

		$laravel_log_viewer = new LaravelLogViewer();
		$laravel_log_viewer->setStoragePath(basename(__DIR__));

		$this->assertEquals("tests", $laravel_log_viewer->getStoragePath());
	}

    public function testPathToLogFile()
	{

		$laravel_log_viewer = new LaravelLogViewer();
        $pathToLogFile = $laravel_log_viewer->pathToLogFile(storage_path(('logs/laravel.log')));
		
        $this->assertEquals($pathToLogFile, storage_path('logs/laravel.log'));
	}

    public function testPathToLogFileWithArrayStoragePath()
	{

		$laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setStoragePath([storage_path()]);
        $pathToLogFile = $laravel_log_viewer->pathToLogFile('laravel.log');

		$this->assertEquals($pathToLogFile, 'laravel.log');
	}

    public function testFailOnBadPathToLogFile()
	{

        $this->expectException(\Exception::class);

		$laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setStoragePath(storage_path());
        $laravel_log_viewer->setFolder('logs');
        $laravel_log_viewer->pathToLogFile('newlogs/nolaravel.txt');
	}

    public function testAll()
    {
        $laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setStoragePath(__DIR__);
        $laravel_log_viewer->pathToLogFile(storage_path('logs/laravel.log'));
        $data = $laravel_log_viewer->all();
        $this->assertEquals('local', $data[0]['context']);
        $this->assertEquals('error', $data[0]['level']);
        $this->assertEquals('danger', $data[0]['level_class']);
        $this->assertEquals('exclamation-triangle', $data[0]['level_img']);
        $this->assertEquals('2018-09-05 20:20:51', $data[0]['date']);
    }

    public function testAllWithEmptyFileName()
    {
        $laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setStoragePath(__DIR__);
        
        $data = $laravel_log_viewer->all();
        $this->assertEquals('local', $data[0]['context']);
        $this->assertEquals('error', $data[0]['level']);
        $this->assertEquals('danger', $data[0]['level_class']);
        $this->assertEquals('exclamation-triangle', $data[0]['level_img']);
        $this->assertEquals('2018-09-05 20:20:51', $data[0]['date']);
    }

    public function testFolderFiles()
    {
        $laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setStoragePath(__DIR__);
        $data = $laravel_log_viewer->foldersAndFiles();
        $this->assertIsArray($data);

        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
        
        $this->assertStringContainsString('tests',  $data[count(explode($data[0], '/')) - 1]);
    }

    public function testGetFolderFiles()
    {
        $laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setStoragePath(__DIR__);
        $data = $laravel_log_viewer->getFolderFiles();
        
        $this->assertIsArray($data);
        $this->assertNotEmpty($data, "Folder files is null");
    }

    public function testGetFiles()
    {
        $laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setStoragePath(storage_path());
        $data = $laravel_log_viewer->getFiles();
  
        $this->assertIsArray($data);
        $this->assertNotEmpty($data, "Folder files is null");
    }

    public function testGetFolders()
    {
        $laravel_log_viewer = new LaravelLogViewer();
        $laravel_log_viewer->setStoragePath(storage_path());
        $data = $laravel_log_viewer->getFolders();
  
        $this->assertIsArray($data);
        $this->assertNotEmpty($data, "files is null");
    }

    public function testDirectoryStructure()
    {
        $log_viewer = new LaravelLogViewer();
        ob_start();
        $log_viewer->directoryTreeStructure(storage_path('logs'), $log_viewer->foldersAndFiles());
        $data = ob_get_clean();
        
        $this->assertIsString($data);
        $this->assertNotEmpty($data);
    }


}
