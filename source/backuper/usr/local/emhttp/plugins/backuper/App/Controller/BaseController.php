<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Src\App;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BaseController
{
    public EntityManager $em;
    private array $appConfig;

    public function __construct()
    {
        $this->appConfig = App::get()->getConfig();
        $this->em = App::get()->getDb()->entityManager;
    }

    public function serialize(mixed $entity, string $format = 'json'): string
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        if (!is_array($entity)) {
            return $serializer->serialize($entity, $format);
        }

        $entities = array_map(function ($singleEntity) use ($serializer, $format) {
            return $serializer->serialize($singleEntity, $format);
        }, $entity);

        return $serializer->encode($entities, $format);
    }

    public function jsonResponse(mixed $data): string
    {
        return json_encode($data);
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
}
