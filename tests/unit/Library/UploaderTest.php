<?php

namespace Library;
/**
 * @covers Library\Uploader
 */

class UploaderTest extends \PHPUnit_Framework_TestCase
{
    private $upload_files;

    private $upload_dir;

    function setUp()
    {
        $_FILES = array(
            'whitelist_test' => array(
                'name' => 'whitelist.jpg',
                'type' => 'image/jpeg',
                'size' => 2185,
                'tmp_name' => __DIR__ . '/whitelist.jpg',
                'error' => 0
            ),
            'nonWhitelist_test' => array(
                'name' => 'nonWhitelist.png',
                'type' => 'image/png',
                'size' => 1298,
                'tmp_name' => __DIR__ . '/nonWhitelist.png',
                'error' => 0
            ),
            'blacklist_test' => array(
                'name' => 'blacklist.php',
                'type' => '',
                'size' => 21,
                'tmp_name' => __DIR__ . '/blacklist.php',
                'error' => 0
            ),
        );

        $this->upload_files = $_FILES;

        $this->upload_dir = __DIR__ . '/test_uploads';
    }

    function tearDown()
    {
        unset($_FILES);
        unset($this->upload_files);
        if(file_exists($this->upload_dir . '/test.jpg'))
        {
            unlink($this->upload_dir . '/test.jpg');
        }
        rmdir($this->upload_dir);
        unset($this->upload_dir);
    }

    function testInstanciation()
    {
        $file = $this->upload_files['whitelist_test'];
        $accepted_file_types = array('jpg');
        $filesize_limit = 1000000;

        $upload = new Uploader('test', $file, $this->upload_dir, $accepted_file_types, $filesize_limit);
        
        $this->assertInstanceOf(Uploader::Class, $upload);
    }

    function testTargetPathIsCreated()
    {
        $file = $this->upload_files['whitelist_test'];
        $accepted_file_types = array('jpg');
        $filesize_limit = 1000000;

        $upload = new Uploader('test', $file, $this->upload_dir, $accepted_file_types, $filesize_limit);
        
        $expected = true;
        $this->assertEquals($expected, file_exists($this->upload_dir));
    }

    function testFailedWhitelistCheck()
    {
        $file = $this->upload_files['nonWhitelist_test'];
        $accepted_file_types = array('jpg');
        $filesize_limit = 1000000;

        $upload = new Uploader('test', $file, $this->upload_dir, $accepted_file_types, $filesize_limit);

        $expected_error = 'Invalid file type.';
        $this->assertEquals($expected_error, $upload->errors[0]);
    }

    function testFileExeedsFilesizeLimit()
    {
        $file = $this->upload_files['whitelist_test'];
        $accepted_file_types = array('jpg');
        $filesize_limit = 10;

        $upload = new Uploader('test', $file, $this->upload_dir, $accepted_file_types, $filesize_limit);
        
        $expected_error = 'Filesize exceeds limit.';
        $this->assertEquals($expected_error, $upload->errors[0]);
    }

    function testFailedBlacklistCheck()
    {
        $file = $this->upload_files['blacklist_test'];
        $accepted_file_types = array('jpg');
        $filesize_limit = 100000;

        $upload = new Uploader('test', $file, $this->upload_dir, $accepted_file_types, $filesize_limit);
        
        $expected_error_found = in_array('File type is not permitted.', $upload->errors);
        $this->assertTrue($expected_error_found);
    }

    function testFailedSave()
    {
        $file = $this->upload_files['whitelist_test'];
        $file['tmp_name'] = __DIR__;
        $accepted_file_types = array('jpg');
        $filesize_limit = 1000000;

        $upload = new Uploader(NULL, $file, $this->upload_dir, $accepted_file_types, $filesize_limit);

        $this->assertFalse($upload->get_uploaded_file());
    }

    function testSave()
    {
        $file = $this->upload_files['whitelist_test'];
        $accepted_file_types = array('jpg');
        $filesize_limit = 1000000;

        $upload = new Uploader('test', $file, $this->upload_dir, $accepted_file_types, $filesize_limit);

        $expected_upload_file_path = $this->upload_dir . '/test.jpg';
        $this->assertEquals($expected_upload_file_path, $upload->get_uploaded_file());
    }
}

?>