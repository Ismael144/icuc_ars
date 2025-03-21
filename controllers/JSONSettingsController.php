<?php

namespace App\controllers;

class JSONSettingsController
{
    public function __construct(public readonly string $settingsFile)
    {
    }

    // Function to read JSON file and decode it into a PHP array
    function readSettings()
    {
        $contents = file_get_contents($this->settingsFile);
        return json_decode($contents, true);
    }

    // Function to write PHP array to JSON file
    function writeSettings($settings)
    {
        $json = json_encode($settings, JSON_PRETTY_PRINT);
        file_put_contents($this->settingsFile, $json);
    }

    // Function to update a specific setting
    function updateSetting($key, $value)
    {
        $settings = $this->readSettings();
        $settings[$key] = $value;
        $this->writeSettings($settings);
    }

    // Function to retrieve a specific setting
    function getSetting($key)
    {
        $settings = $this->readSettings();
        return isset($settings[$key]) ? $settings[$key] : null;
    }
}