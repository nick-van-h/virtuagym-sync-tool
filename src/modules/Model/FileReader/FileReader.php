<?php

namespace Vst\Model;

/**
 * Use Strategy pattern to get specific files, each file has their own class
 * Use Dependency Injection to pass the specific file reader class to the new parent class
 */
interface FileReader
{
    public function __construct();
    public function getFileContent();
}
