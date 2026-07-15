<?

declare(strict_types=1);

namespace Framework;

use ReflectionClass,  ReflectionNamedType;
use Framework\Exceptions\ContainerException;

class Container
{
    private array $difinitions = [];
    private array $resolved = [];

    public function addDefinition(array $newDefitition)
    {
        //Merges two arrays but this was is faster then the array_merge function
        $this->difinitions = [...$this->difinitions, ...$newDefitition];
    }


    /**
     * dependency injection to create classes on the fly
     * 
     * @param string $className
     * 
     * @return TemplateEngine: class that was requested with params 
     */
    public function resolve(string $className)
    {
        $reflectionClass = new ReflectionClass($className);

        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException("Class {$className} is not instantiable", 400);
        }

        $constructor = $reflectionClass->getConstructor();

        if (!$constructor) {
            return new $className;
        }

        $parameters = $constructor->getParameters();

        if (count($parameters) == 0) {
            return new $className;
        }

        $dependencies = [];

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            if (!$type) {
                throw new ContainerException("Failed to resolve class {$className} because param {$name} is missing a type hint.", 400);
            }

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new ContainerException("Failed to resolve class {$className} because invalid param name.", 400);
            }

            $dependencies[] = $this->get($type->getName());
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }


    /**
     * 
     * @param string $id: id in the array of difinitions
     * 
     * @return TemplateEngine: dependency
     */
    public function get(string $id)
    {
        if (!array_key_exists($id, $this->difinitions)) {
            throw new ContainerException("Class {$id} does not exist in the container.", 400);
        }

        if (array_key_exists($id, $this->resolved)) {
            return $this->resolved[$id];
        }

        $factory = $this->difinitions[$id];
        $dependency = $factory($this);
        $this->resolved[$id] = $dependency;

        return $dependency;
    }
}
