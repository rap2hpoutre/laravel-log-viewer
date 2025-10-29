<?php

namespace Rap2hpoutre\LaravelLogViewer\Tests;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;

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
        if(!File::exists("$path/samuel")) File::makeDirectory("$path/samuel");
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

    /**
     * Test that level names appearing in log message text are not incorrectly
     * parsed as separate log levels. This tests the fix for the bug where
     * words like "processed" or "failed" in messages were creating false log entries.
     */
    public function testLevelNamesInMessageTextNotParsedAsLevels()
    {
        // Create a temporary log file with entries containing level names in the message
        $testLogPath = storage_path('logs/test-level-parsing.log');
        $testLogContent = <<<'LOG'
[2025-10-28 20:59:22] local.INFO: Redirect map successfully rebuilt and cached. Entries processed: 6866, Aliases mapped: 15918
[2025-10-28 20:59:23] local.WARNING: Job processing failed with error code 500
[2025-10-28 20:59:24] local.DEBUG: Processing diagnostic information
LOG;

        File::put($testLogPath, $testLogContent);

        try {
            $laravel_log_viewer = new LaravelLogViewer();
            $laravel_log_viewer->setFile($testLogPath);
            $data = $laravel_log_viewer->all();

            // Should only have 3 log entries (not more due to false "processed" or "failed" matches)
            $this->assertCount(3, $data, 'Should only parse 3 log entries, not create false entries from level names in message text');

            // Verify the first entry is correctly parsed as INFO level, not "processed"
            $this->assertEquals('info', $data[2]['level'], 'First entry should be INFO level');
            $this->assertEquals('local', $data[2]['context']);
            $this->assertStringContainsString('Entries processed: 6866', $data[2]['text'], 'Message text should contain "processed" but not be parsed as a level');

            // Verify the second entry is correctly parsed as WARNING level, not "failed"
            $this->assertEquals('warning', $data[1]['level'], 'Second entry should be WARNING level');
            $this->assertEquals('local', $data[1]['context']);
            $this->assertStringContainsString('Job processing failed', $data[1]['text'], 'Message text should contain "failed" but not be parsed as a level');

            // Verify the third entry is correctly parsed as DEBUG level
            $this->assertEquals('debug', $data[0]['level'], 'Third entry should be DEBUG level');

            // Ensure no entries have "processed" or "failed" as their level
            foreach ($data as $entry) {
                $this->assertNotEquals('processed', $entry['level'], 'No entry should have "processed" as a level');
                $this->assertNotEquals('failed', $entry['level'], 'No entry should have "failed" as a level');
            }
        } finally {
            // Clean up the test log file
            if (File::exists($testLogPath)) {
                File::delete($testLogPath);
            }
        }
    }

}
