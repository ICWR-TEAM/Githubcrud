<?php

namespace IcwrTeam\Githubcrud;

class Githubcrud
{
    private $gitConfig;

    public function __construct(array $gitConfig)
    {
        $this->gitConfig = $gitConfig;
    }

    private function makeCurlRequest(string $url, string $method, array $data = [], bool $decodeResponse = true): mixed
    {
        $ch = curl_init(url: $url);

        curl_setopt_array(handle: $ch, options: [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Authorization: token ' . $this->gitConfig['token'],
                'User-Agent: PHP Script',
                'Content-Type: application/json',
            ],
        ]);

        if (!empty($data)) {
            curl_setopt(handle: $ch, option: CURLOPT_POSTFIELDS, value: json_encode(value: $data));
        }

        $response = curl_exec(handle: $ch);
        $httpCode = curl_getinfo(handle: $ch, option: CURLINFO_HTTP_CODE);
        curl_close(handle: $ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return $decodeResponse ? json_decode(json: $response, associative: true) : $response;
        }

        throw new \Exception(message: "GitHub API Request failed. HTTP Code: $httpCode. Response: $response");
    }

    public function createFile(string $fileName, string $fileContent): bool
    {
        $url = "https://api.github.com/repos/{$this->gitConfig['username']}/{$this->gitConfig['repository']}/contents/$fileName";

        $data = [
            'message' => "Create file $fileName",
            'content' => base64_encode(string: $fileContent),
            'branch' => $this->gitConfig['branch'],
        ];

        $this->makeCurlRequest(url: $url, method: 'PUT', data: $data);
        return true;
    }

    public function editFile(string $fileName, string $fileContent): bool
    {
        $url = "https://api.github.com/repos/{$this->gitConfig['username']}/{$this->gitConfig['repository']}/contents/$fileName";

        // Get file info to retrieve the SHA
        $fileInfo = $this->makeCurlRequest(url: $url, method: 'GET');
        $fileSha = $fileInfo['sha'];

        $data = [
            'message' => "Edit file $fileName",
            'content' => base64_encode(string: $fileContent),
            'sha' => $fileSha,
            'branch' => $this->gitConfig['branch'],
        ];

        $this->makeCurlRequest(url: $url, method: 'PUT', data: $data);
        return true;
    }

    public function readFile(string $fileName): string
    {
        $url = "https://api.github.com/repos/{$this->gitConfig['username']}/{$this->gitConfig['repository']}/contents/$fileName?ref={$this->gitConfig['branch']}";

        $fileInfo = $this->makeCurlRequest(url: $url, method: 'GET');
        return base64_decode(string: $fileInfo['content']);
    }

    public function deleteFile(string $fileName): bool
    {
        $url = "https://api.github.com/repos/{$this->gitConfig['username']}/{$this->gitConfig['repository']}/contents/$fileName";

        // Get file info to retrieve the SHA
        $fileInfo = $this->makeCurlRequest(url: $url, method: 'GET');
        $fileSha = $fileInfo['sha'];

        $data = [
            'message' => "Delete file $fileName",
            'sha' => $fileSha,
            'branch' => $this->gitConfig['branch'],
        ];

        $this->makeCurlRequest(url: $url, method: 'DELETE', data: $data);
        return true;
    }
}
