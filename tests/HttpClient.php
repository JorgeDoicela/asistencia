<?php

// Cliente HTTP simple para pruebas End-to-End
// Permite simular navegadores con sesiones mediante cookies

class HttpClient
{
    private string $baseUrl;
    private string $cookieFile;

    public function __construct(string $baseUrl = 'http://127.0.0.1')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'test_cookie_');
    }

    public function __destruct()
    {
        if (file_exists($this->cookieFile)) {
            @unlink($this->cookieFile);
        }
    }

    public function get(string $ruta): array
    {
        return $this->request('GET', $ruta);
    }

    public function post(string $ruta, array $datos = []): array
    {
        return $this->request('POST', $ruta, $datos);
    }

    private function request(string $metodo, string $ruta, array $datos = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($ruta, '/');
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // No seguir redirects automaticos para poder verificar 302
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);

        if ($metodo === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datos));
        }

        $respuesta = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $headersRaw = substr($respuesta, 0, $headerSize);
        $body = substr($respuesta, $headerSize);

        curl_close($ch);

        return [
            'codigo'  => $httpCode,
            'headers' => $headersRaw,
            'body'    => $body
        ];
    }
}
