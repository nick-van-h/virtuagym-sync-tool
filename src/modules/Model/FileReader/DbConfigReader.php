<?php

namespace Vst\Model;

class DbConfigReader implements FileReader
{
    private const FILE = CONFIG_FILE;

    public function __construct()
    {
    }

    public function getFileContent()
    {
        return json_decode(file_get_contents(self::FILE));
    }
}
