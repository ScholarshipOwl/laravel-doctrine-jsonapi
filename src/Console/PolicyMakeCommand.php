<?php

namespace Sowl\JsonApi\Console;

use Illuminate\Foundation\Console\PolicyMakeCommand as LaravelPolicyMakeCommand;
class PolicyMakeCommand extends LaravelPolicyMakeCommand
{
    protected $name = 'jsonapi:make:policy';
    protected static $defaultName = 'jsonapi:make:policy';

    protected $description = 'Create a new resource policy class';
}