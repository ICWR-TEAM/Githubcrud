<?php

namespace Afrzlfa\Githubcrud;

class Githubcrud
{
    private $gitConfig;

    public function __construct(array $gitConfig)
    {
        $this->gitConfig = $gitConfig;
    }

    private function makeCurlRequest(string $url, string $method, array $data = [], bool $decodeResponse = true)
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Authorization: token ' . $this->gitConfig['token'],
                'User-Agent: PHP Script',
                'Content-Type: application/json',
            ],
        ]);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return $decodeResponse ? json_decode($response, true) : $response;
        }

        throw new \Exception("GitHub API Request failed. HTTP Code: $httpCode. Response: $response");
    }

    public function createFile(string $fileName, string $fileContent): bool
    {
        $url = "https://api.github.com/repos/{$this->gitConfig['username']}/{$this->gitConfig['repository']}/contents/$fileName";

        $data = [
            'message' => "Create file $fileName",
            'content' => base64_encode($fileContent),
            'branch' => $this->gitConfig['branch'],
        ];

        $this->makeCurlRequest($url, 'PUT', $data);
        return true;
    }

    public function editFile(string $fileName, string $fileContent): bool
    {
        $url = "https://api.github.com/repos/{$this->gitConfig['username']}/{$this->gitConfig['repository']}/contents/$fileName";

        // Get file info to retrieve the SHA
        $fileInfo = $this->makeCurlRequest($url, 'GET');
        $fileSha = $fileInfo['sha'];

        $data = [
            'message' => "Edit file $fileName",
            'content' => base64_encode($fileContent),
            'sha' => $fileSha,
            'branch' => $this->gitConfig['branch'],
        ];

        $this->makeCurlRequest($url, 'PUT', $data);
        return true;
    }

    public function readFile(string $fileName): string
    {
        $url = "https://api.github.com/repos/{$this->gitConfig['username']}/{$this->gitConfig['repository']}/contents/$fileName?ref={$this->gitConfig['branch']}";

        $fileInfo = $this->makeCurlRequest($url, 'GET');
        return base64_decode($fileInfo['content']);
    }

    public function deleteFile(string $fileName): bool
    {
        $url = "https://api.github.com/repos/{$this->gitConfig['username']}/{$this->gitConfig['repository']}/contents/$fileName";

        // Get file info to retrieve the SHA
        $fileInfo = $this->makeCurlRequest($url, 'GET');
        $fileSha = $fileInfo['sha'];

        $data = [
            'message' => "Delete file $fileName",
            'sha' => $fileSha,
            'branch' => $this->gitConfig['branch'],
        ];

        $this->makeCurlRequest($url, 'DELETE', $data);
        return true;
    }
}
