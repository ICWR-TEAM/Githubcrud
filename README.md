# GitHub CRUD Library

A simple PHP library for managing GitHub repository files using the GitHub API.

## Installation

Install the library using Composer:

```bash
composer require afrzlfa/githubcrud
```

## Usage

Here's an example of how to use the library:

```php
require 'vendor/autoload.php';

use Afrzlfa\Githubcrud;

$config = [
    'token' => 'YOUR_GITHUB_TOKEN',
    'username' => 'YOUR_GITHUB_USERNAME',
    'repository' => 'YOUR_REPOSITORY_NAME',
    'branch' => 'main' // or any branch name
];

$github = new Afrzlfa\Githubcrud\Githubcrud($config);

// Create a file
$github->createFile('example.txt', 'Hello, World!');

// Edit a file
$github->editFile('example.txt', 'Updated content');

// Read a file
$content = $github->readFile('example.txt');

echo $content;

// Delete a file
$github->deleteFile('example.txt');
```

## Requirements

- PHP 7.4 or higher
- Composer

## License

This library is open-sourced software licensed under the [MIT license](LICENSE).

