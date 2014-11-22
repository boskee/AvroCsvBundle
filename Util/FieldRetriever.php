<?php

namespace Avro\CsvBundle\Util;

use Doctrine\Common\Annotations\AnnotationReader;

use Avro\CsvBundle\Annotation\ImportExclude;
use Avro\CaseBundle\Util\CaseConverter;

/**
 * Retrieves the fields of a Doctrine entity/document that
 * are allowed to be imported
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class FieldRetriever
{
    protected $annotationReader;
    protected $caseConverter;

    /**
     * @param AnnotationReader $annotationReader The annotation reader service
     * @param CaseConverter    $caseConverter    The caseConverter service
     */
    public function __construct($annotationReader, CaseConverter $caseConverter)
    {
        $this->annotationReader = $annotationReader;
        $this->caseConverter = $caseConverter;
    }

    /**
     * Get the entity/documents field names
     *
     * @param string  $class     The class name
     * @param string  $alias     The alias
     * @param string  $format    The desired field case format
     * @param boolean $copyToKey Copy the field values to their respective key
     *
     * @return array $fields
     */
    public function getFields($class, $alias, $format = 'title', $copyToKey = false)
    {
        $reflectionClass = new \ReflectionClass($class);
        $properties = $reflectionClass->getProperties();

        $fields = array();
        $translationKey = array();
        $fields[] = '';
        $translationKey[] = 'import.field.skip';

        foreach ($properties as $property) {
            $addField = true;
            foreach ($this->annotationReader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof ImportExclude) {
                    $addField = false;
                }
            }

            if ($addField) {
                $field = $this->caseConverter->convert($property->getName(), $format);
                $fields[] = $field;
                $translationKey[] = sprintf('import.field.%s.%s', $alias, strtolower($field));
            }
        }

        if ($copyToKey) {
            $fields = array_combine($fields, $translationKey);
        }

        return $fields;
    }
}
