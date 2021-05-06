<?php

$finder = PhpCsFixer\Finder::create()
    ->in(['src', 'tests'])
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        '@PSR12:risky' => true,
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        'ordered_imports' => false,
        'no_superfluous_phpdoc_tags' => false,
        'phpdoc_align' => false,
        'single_line_throw' => false,
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => true,
        'no_trailing_whitespace_in_string' => false,
        'no_unreachable_default_argument_value' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'phpdoc_summary' => false,
        'no_useless_else' => true,
        'simplified_if_return' => true,
        'no_alias_functions' => true,
        'is_null' => true,
        'return_assignment' => true,
        'no_superfluous_elseif' => true,
        'operator_linebreak' => ['position' => 'beginning'],
        'array_indentation' => true,
        'ordered_class_elements' => [
            'use_trait',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public',
            'property_protected',
            'property_private',
            'construct',
        ],
        'multiline_comment_opening_closing' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'phpdoc_line_span' => ['property' => 'multi'],
        'no_useless_sprintf' => true,
        'phpdoc_to_comment' => false,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'logical_operators' => true,
        'phpdoc_order' => true,
        'ordered_imports' => ['sort_algorithm' => 'length'],
    ])
    ->setFinder($finder)
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
;
