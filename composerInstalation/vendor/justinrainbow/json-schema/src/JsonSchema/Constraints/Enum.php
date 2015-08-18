<?php








namespace JsonSchema\Constraints;







class Enum extends Constraint
{



public function check($element, $schema = null, $path = null, $i = null)
{

 if ($element instanceof Undefined && (!isset($schema->required) || !$schema->required)) {
return;
}

foreach ($schema->enum as $enum) {
if ((gettype($element) === gettype($enum)) && ($element == $enum)) {
return;
}
}

$this->addError($path, "does not have a value in the enumeration " . print_r($schema->enum, true));
}
}