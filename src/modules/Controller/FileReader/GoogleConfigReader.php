<?php

namespace Vst\Controller;

class GoogleConfigReader implements FileReader
{
    private const FILE = OAUTH_FILE;

    public function __construct()
    {
    }

    public function getFileContent()
    {
        return json_decode(file_get_contents(self::FILE));
    }
}
