<?php
$finder = Symfony\CS\Finder\DefaultFinder::create();
$config = Symfony\CS\Config\Config::create();
$config->level(null);
$config->fixers(
    array(encoding,indentation,linefeed,trailing_spaces,short_tag,visibility,php_closing_tag,braces,function_declaration,psr0,elseif,eof_ending,unused_use,phpdoc_indent,multiline_array_trailing_comma,method_argument_space,short_array_syntax,)
);
$config->finder($finder);
return $config;
