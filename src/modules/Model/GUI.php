<?php

namespace Vst\Model;

class GUI {
    private $changeLog;

    public __construct() {
        $this->changelog = getChangelog();
    }
    
    
    function getChangelog($lastVisitedVersion) {
        $changelog = '';

        foreach($this->changelog as $version) {
            //Release summary
            $changelog .= $version . ' (' . $version->released . ')/n';
        }
        $input = fopen(README, 'r');
        $lastVersion = null;

        while (!feof($input)) {
            $line = fgets($input);

            /**
             * Check if current line item contains a version number
             * If version is last visited version don't look any further
             */
            if (pgreg_match('/[' . CHANGELOG_VERSION_START . ']/', $input)) {
                $lastVersion = $curVersion;
                $curVersion = preg_match('/[%]/', $input);
            }
            if ($curVersion ==$lastVisitedVersion) break;

            /**
             * Add the line item to the changelog summary
             */
            if (!isnull($lastVersion) && $lastVersion <> CHANGELOG_VERSION_UPCOMING) {
                $changelog .= $line . '/n';
            }
        }

        //Close the file

        return $changelog
    }
}