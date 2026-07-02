<?php

use PhpCsFixer\Config;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/src')
    ->exclude('var')
;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'declare_strict_types' => true,
        'unary_operator_spaces' => false,
        'single_line_throw' => false,
        'phpdoc_no_alias_tag' => [
            'replacements' => [
                'property-read' => 'property',
                'property-write' => 'property',
                'type' => 'var',
            ],
        ],
        'yoda_style' => [
            'equal' => null,
            'identical' => null,
            'less_and_greater' => null,
        ],
        'global_namespace_import' => [
            'import_classes' => true,
        ],
        'class_attributes_separation' => [
            'elements' => [
                'trait_import' => 'none',
                'const' => 'none',
                'method' => 'one',
                'property' => 'none',
            ],
        ],
    ])
    ->setFinder($finder);