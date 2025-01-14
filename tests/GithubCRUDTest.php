<?php

require 'vendor/autoload.php';

use Afrzlfa\Githubcrud;

$config = [
    'token' => 'YOUR_GITHUB_TOKEN',
    'username' => 'YOUR_USERNAME',
    'repository' => 'YOUR_REPOSITORY',
    'branch' => 'main'
];

$github = new Afrzlfa\Githubcrud\Githubcrud($config);

// Create a file
$github->createFile('example.txt', 'Hello, World!');
