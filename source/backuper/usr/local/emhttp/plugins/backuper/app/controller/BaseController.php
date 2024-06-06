<?php

namespace App\Controller;

use App\App;
use App\Entity\BaseEntity;
use App\Entity\EntityManager;

class BaseController
{
    private array $appConfig;
    private EntityManager $entityManager;

    public function __construct()
    {
        $this->appConfig = App::get()->getConfig();
        $this->entityManager = new EntityManager();
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
        $value = $this->serialize($value);

        $value = json_encode($value);

        echo "<input type='hidden' id='{$key}' data-{$key}='{$value}' />";
    }

    private function serialize($value): mixed
    {
        if (is_array($value)) {
            return array_map([$this, 'serialize'], $value);
        }

        if (gettype($value) !== "object") { return $value; }

        if (!is_a($value, BaseEntity::class)) { return $value; }

        $basename = $this->entityManager->classBaseName($value);
        $serializerClass = "App\\Serializer\\{$basename}Serializer";

        if (!class_exists($serializerClass)) {
            throw new \Exception("Now serializer found for $basename.");
        }

        return (new $serializerClass)->serialize($value);
    }
}
