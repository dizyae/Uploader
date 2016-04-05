<?php

namespace Library;

class Uploader {

    private $target_file;

    public $errors;

    function __construct($name, $upload_file, $upload_dir, $whitelist, $size_limit)
    {
        $this->errors = array();
        $this->upload_file = $upload_file;
        
        $this->set_target($name, $upload_dir);
        $this->set_whitelist($whitelist);
        $this->check_blacklist();
        $this->set_filesize_limit($size_limit);
        $this->save_file();
    }

    private function set_target($filename, $upload_dir)
    {
        $filename_array = explode('.', $this->upload_file['name']);
        $file_ext = $filename_array[count($filename_array) - 1];

        if(!file_exists($upload_dir))
        {
            mkdir($upload_dir, 0777, true);
        }

        $this->target_file = $upload_dir . '/' . basename($filename . '.' . $file_ext);
    }

    private function get_file_type()
    {
        return pathinfo($this->target_file, PATHINFO_EXTENSION);
    }

    private function get_filesize()
    {
        return filesize($this->upload_file['tmp_name']);
    }

    private function set_whitelist($accepted_types)
    {
        if(!in_array($this->get_file_type(), $accepted_types))
        {
            $this->errors[] = 'Invalid file type.';
        }
    }

    private function set_filesize_limit($limit)
    {
        if(!$this->get_filesize() || $this->get_filesize() > $limit)
        {
            $this->errors[] = 'Filesize exceeds limit.';
        }    
    }

    private function check_blacklist()
    {
        if(strpos($this->upload_file['name'], '.php') !== false || strpos($this->upload_file['name'], '.sql') !== false)
        {
            $this->errors[] = 'File type is not permitted.';
        }
    }

    private function save_file()
    {
        if(empty($this->errors))
        {
            if(file_exists($this->target_file) || !move_uploaded_file($this->upload_file['tmp_name'], $this->target_file))
            {
                $this->errors[] = 'File was not uploaded, please try again.';
                $this->target_file = false;
            }
        }
        else
        {
            $this->target_file = false;
        }
    }

    public function get_uploaded_file()
    {
        return $this->target_file;
    }
}

?>