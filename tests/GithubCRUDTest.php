<?php

require 'vendor/autoload.php';

$config = [
    'token' => 'YOUR_GITHUB_TOKEN',
    'username' => 'YOUR_USERNAME',
    'repository' => 'YOUR_REPOSITORY',
    'branch' => 'main'
];

$github = new IcwrTeam\Githubcrud\Githubcrud(gitConfig: $config);

// Create a file
$github->createFile(fileName: 'example.txt', fileContent: 'Hello, World!');
