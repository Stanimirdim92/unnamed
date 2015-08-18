<?php








namespace JsonSchema;

use JsonSchema\Constraints\Schema;
use JsonSchema\Constraints\Constraint;

use JsonSchema\Exception\InvalidSchemaMediaTypeException;
use JsonSchema\Exception\JsonDecodingException;

use JsonSchema\Uri\Retrievers\UriRetrieverInterface;








class Validator extends Constraint
{
const SCHEMA_MEDIA_TYPE = 'application/schema+json';








public function check($value, $schema = null, $path = null, $i = null)
{
$validator = new Schema($this->checkMode, $this->uriRetriever);
$validator->check($value, $schema);

$this->addErrors(array_unique($validator->getErrors(), SORT_REGULAR));
}
}
