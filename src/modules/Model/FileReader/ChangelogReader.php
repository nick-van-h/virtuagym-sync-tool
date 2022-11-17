<?php

namespace Vst\Model;

class ChangelogReader implements FileReader
{
    private const FILE = CHANGELOG_FILE;

    public function __construct()
    {
    }

    public function getFileContent()
    {
        return json_decode(file_get_contents(self::FILE));
    }
}
