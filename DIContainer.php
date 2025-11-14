<?php
/**
 * Usage:
 *  (new DIContainer())
 *     ->get(MyLoggerWithContext::class);
 *
 * Or:
 *  (new DIContainer('x-type'))
 *     ->get(MyLoggerWithContext::class);
 */
class DIContainer
{
    /* example app name for use with multiple apps */
    public const string MY_APP_TYPE= 'MY_APP_TYPE';
    public const string MY_APP_TYPE_LEGACY = 'MY_APP_TYPE_LEGACY';
    protected array $instances = [];
    protected string $configType;

    public function __construct(?string $configType = self::MY_APP_TYPE)
    {
        $this->configType = $configType ?? self::MY_APP_TYPE;
    }

    /* Example mapping for interfaces, etc. todo - put in config file */
    protected const array MY_APP_TYPE_CONFIG_ONE = [
       // \Psr\Log\LoggerInterface::class => MyPSRLogger::class,
       // \Illuminate\Log\Logger::class => MyPSRLogger::class,
       // \App\Caching\CachingRedisAdapter::class => MyLegacyCachingRedisAdapter::class,
    ];

    /* Another example mapping for interfaces, etc. */
    protected const array MY_APP_TYPE_CONFIG_TWO = [
       // \Psr\Log\LoggerInterface::class => MyLegacyPSRLogger::class,
       // \Illuminate\Log\Logger::class => MyLegacyPSRLogger::class,
       // \App\Caching\CachingRedisAdapter::class => MyLegacyCachingRedisAdapter::class,
    ];

    protected function getMappingConfig(string $configType): array
    {
        return match ($configType) {
            DIContainer::MY_APP_TYPE_LEGACY => self::MY_APP_TYPE_CONFIG_TWO,
            default => self::MY_APP_TYPE_CONFIG_ONE,
        };
    }

    protected function set(string $className, $concrete = null): void
    {
        $this->instances[$className] = $concrete;
    }

    protected function getMapFor($id): ?string
    {
        return $this->getMappingConfig($this->configType)[$id] ?? null;
    }

    /** @throws Exception */
    public function get(string $id, $parameters = [])
    {
        !empty($this->getMapFor($id)) && $id = $this->getMapFor($id);
        if ($this->has($id)) {
            return $this->instances[$id];
        }
        $this->set($id, $this->resolve($id, $parameters));
        return $this->instances[$id];
    }

    /**
     * If you need to use an existing object to build a new one with existing state, pass it in via $objects
     * E.g.:
     *    $reportingService = $diContainer->getWithExistingContext(ReportingService::class, [$logAggregator]);
     * @throws Exception
     */
    public function getWithExistingContext(string $id, array $objects = [], array $parameters = [])
    {
        !empty($this->oneRosterConfig[$id]) && $id = $this->oneRosterConfig[$id];
        if ($this->has($id)) {
            return $this->instances[$id];
        }
        $this->set($id, $this->resolve($id, $parameters, $objects));
        return $this->instances[$id];
    }

    /** @throws Exception */
    protected function resolve($id, array $parameters, array $objects = [])
    {
        if ($id instanceof Closure) {
            return $id($this, $parameters);
        }

        $reflector = new ReflectionClass($id);
        if (!$reflector->isInstantiable()) {
            throw new Exception("Class $id is not instantiable");
        }

        if (is_null($constructor = $reflector->getConstructor())) {
            return $reflector->newInstance();
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters, $objects);

        return $reflector->newInstanceArgs($dependencies);
    }

    /** @throws Exception */
    protected function getDependencies($parameters, array $objects = []): array
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependencyType = $parameter->getType();
            if (is_null($dependencyType)) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Can not resolve class dependency $parameter->name");
                }
            } else {
                $map = [];
                array_map(function($obj) use (&$map) {
                    $map[get_class($obj)] = $obj;
                }, $objects);
                $dependencies[] = empty($map[$dependencyType->getName()]) ? $this->get($dependencyType->getName()) : $map[$dependencyType->getName()];
            }
        }

        return $dependencies;
    }

    public function has(string $id): bool
    {
        return !empty($this->instances[$id]);
    }
}