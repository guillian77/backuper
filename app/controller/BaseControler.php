<?php

namespace App\Controller;

use App\App;

class BaseControler
{
    private array $appConfig;

    public function __construct()
    {
        $this->appConfig = App::get()->getConfig();
    }

    public function render(string $view, array $parameters = []): string
    {
        $viewPath = $this->appConfig['view_path'] .DIRECTORY_SEPARATOR . $view;

        if (!file_exists($viewPath)) {
            return "{$viewPath} does not exist.";
        }

        // Start recording document.
        ob_start();

        // Extract render parameters has variable.
        extract($parameters);

        // Defining some template helper functions.
        $varToJavascript = function (...$params) { $this->addVariableToTemplate(...$params); };

        require $viewPath;

        return ob_get_clean();
    }

    private function addVariableToTemplate(string $key, mixed $value)
    {
        (is_array($value)) && $value = json_encode($value);

        echo "<input type='hidden' id='{$key}' data-{$key}='{$value}' />";
    }
}
