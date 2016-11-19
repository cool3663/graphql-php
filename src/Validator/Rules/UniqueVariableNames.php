<?php
namespace GraphQL\Validator\Rules;

use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NodeType;
use GraphQL\Language\AST\VariableDefinitionNode;
use GraphQL\Validator\ValidationContext;

class UniqueVariableNames
{
    static function duplicateVariableMessage($variableName)
    {
        return "There can be only one variable named \"$variableName\".";
    }

    public $knownVariableNames;

    public function __invoke(ValidationContext $context)
    {
        $this->knownVariableNames = [];

        return [
            NodeType::OPERATION_DEFINITION => function() {
                $this->knownVariableNames = [];
            },
            NodeType::VARIABLE_DEFINITION => function(VariableDefinitionNode $node) use ($context) {
                $variableName = $node->variable->name->value;
                if (!empty($this->knownVariableNames[$variableName])) {
                    $context->reportError(new Error(
                        self::duplicateVariableMessage($variableName),
                        [ $this->knownVariableNames[$variableName], $node->variable->name ]
                    ));
                } else {
                    $this->knownVariableNames[$variableName] = $node->variable->name;
                }
            }
        ];
    }
}