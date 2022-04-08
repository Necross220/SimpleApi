<?php

class Router extends connection
{
    Protected Mixed $controller;

    /**
     * Parses the URL address using slashes and returns params as array
     * @param string $url The URL address to be parsed
     * @return array The URL parameters
     */
    private function parseUrl(string $url): array
    {
        // Parses URL parts into an associative array
        $parse_url = parse_url($url);
        // Removes the leading slash
        $parse_url['path'] = ltrim($parse_url['path'], '/');
        // Removes white-spaces around the address
        $parse_url['path'] = trim($parse_url['path']);
        // Splits the address by slashes
        return explode('/', $parse_url['path']);
    }

    /**
     * Parses the URL address and creates appropriate controller
     * @param array $params The URL address as an array of a single element
     */
    public function process(array $params): void
    {
        $parse_url = $this->parseUrl($params[0]);

        $controller = $parse_url[0];

        if (file_exists("controllers/{$controller}.controller.php")) {
            $this->controller = new $controller;
            $this->controller->process($parse_url);
        }
    }
}