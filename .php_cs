<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in('config')
    ->in('module')
    ->in('public')
    ->notPath('vendor')
    ->filter(function (SplFileInfo $file) {
        if (strstr($file->getPath(), 'compatibility')) {
            return false;
        }
    });

$config = Symfony\CS\Config\Config::create();
$config->level(null);
$config->fixers(
    array(
        'encoding',
        'indentation',
        'linefeed',
        'trailing_spaces',
        'short_tag',
        'visibility',
        'php_closing_tag',
        'braces',
        'function_declaration',
        'elseif',
        'eof_ending',
        'unused_use',
        'phpdoc_indent',
        'multiline_array_trailing_comma',
        'method_argument_space',
        'short_array_syntax',
        'single_blank_line_before_namespace',
        'duplicate_semicolon',
        'empty_return',
        'function_call_space',
        'line_after_namespace',
        'lowercase_keywords',
    )
    // array(
    //     'join_function',
    //     'parenthesis',
    //     'multiple_use',
    //     'object_operator',
    //     'remove_lines_between_uses',
    //     'standardize_not_equal',
    //     'whitespacy_lines',
    // )
);
$config->finder($finder);
return $config;
