# PHP-DI-Container
### Usage
```php
// Usage:
 $logger = (new DIContainer())
    ->get(MyLoggerWithContext::class);
// Or:
 $logger = (new DIContainer('x-type'))
    ->get(MyLoggerWithContext::class);
```

### Overwrite or Map Interfaces 
(WIP - It works, but needs to be extracted into separate config file)  
Currently done inside the DIContainer, which you can extend if you wanted to via:
```php
class MyDiContainer extends \DIContainer\DIContainer
{
...
    /* Add mapping */
    protected const array MY_APP_TYPE_CONFIG_TWO = [
        \Psr\Log\LoggerInterface::class => MyLegacyPSRLogger::class,
        \Illuminate\Log\Logger::class => MyLegacyPSRLogger::class,
        \MyApp\Caching\RedisAdapter::class => MyLegacyRedisAdapter::class,
    ];
    
    /* map file here */
    protected function getMappingConfig(string $configType): array
    {
        return match ($configType) {
            DIContainer::MY_APP_TYPE_LEGACY => self::MY_APP_TYPE_CONFIG_TWO,
            default => self::MY_APP_TYPE_CONFIG_ONE,
        };
    }
... 
```

And then later use: 
```php
$logger = (new MyDiContainer('MY_APP_TYPE'))->get(MyLoggerWithContext::class);
```

### Running tests
```php
composer test

Runtime:       PHP 8.4.14
..                                                                  2 / 2 (100%)

Time: 00:00, Memory: 14.00 MB

OK (2 tests, 2 assertions)
```