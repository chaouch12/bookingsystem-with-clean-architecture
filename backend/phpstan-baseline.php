<?php declare(strict_types = 1);

$ignoreErrors = [];

$ignoreErrors[] = [
    'message' => '#^Class UserRepository has PHPDoc tag @method for method findBy\(\) parameter \#1 \$criteria with no value type specified in iterable type array\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/src/Layers/Domain/Users/Repository/UserRepository.php',
];

$ignoreErrors[] = [
    'message' => '#^Class UserRepository has PHPDoc tag @method for method findBy\(\) parameter \#2 \$orderBy with no value type specified in iterable type array\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/src/Layers/Domain/Users/Repository/UserRepository.php',
];

$ignoreErrors[] = [
    'message' => '#^Class UserRepository has PHPDoc tag @method for method findOneBy\(\) parameter \#1 \$criteria with no value type specified in iterable type array\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/src/Layers/Domain/Users/Repository/UserRepository.php',
];

$ignoreErrors[] = [
    'message' => '#^Class UserRepository has PHPDoc tag @method for method findOneBy\(\) parameter \#2 \$orderBy with no value type specified in iterable type array\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/src/Layers/Domain/Users/Repository/UserRepository.php',
];

$ignoreErrors[] = [
    'message' => '#^Method UserRepository::getUsersByIds\(\) has parameter \$ids with no value type specified in iterable type array\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/src/Layers/Domain/Users/Repository/UserRepository.php',
];

$ignoreErrors[] = [
    'message' => '#^Method UserRepository::getUsersByIds\(\) return type has no value type specified in iterable type array\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/src/Layers/Domain/Users/Repository/UserRepository.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];