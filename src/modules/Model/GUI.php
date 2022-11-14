<?php

namespace Vst\Model;

class GUI
{
    private $changelog;

    public function __construct()
    {
        $this->changelog = getChangelog();
    }


    function getChangelog($lastVisitedVersion = null)
    {
        $changelog = '';

        foreach ($this->changelog as $version => $change) {
            if (($version > $lastVisitedVersion || is_null($lastVisitedVersion)) && $version != CHANGELOG_VERSION_UPCOMING) {
                //Version + date & summary
                $changelog .= '<h2>' . $version . ' (' . $change->released . ')</h2>';
                $changelog .= '<p>' . $change->summary . '<p>';

                //Sum changes for that version
                foreach ($change as $key => $val) {
                    if (is_array($val)) {
                        $changelog .= '<h3>' . ucfirst($key) . '</h3>';
                        $changelog .= '<ul>';
                        foreach ($val as $item) {
                            $changelog .= '<li>' . $item . '</li>';
                        }
                        $changelog .= '</ul>';
                    }
                }
            }
        }
        return $changelog;
    }

    function getLatestReleasedVersion()
    {
        $latestVersion = '1.0.0';
        foreach ($this->changelog as $version => $change) {
            if ($version != CHANGELOG_VERSION_UPCOMING) {
                $latestVersion = ($version > $latestVersion) ? $version : $latestVersion;
            }
        }
        return $latestVersion;
    }
}
