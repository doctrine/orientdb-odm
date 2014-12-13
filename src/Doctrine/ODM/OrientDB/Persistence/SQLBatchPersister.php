<?php

namespace Doctrine\ODM\OrientDB\Persistence;


use Doctrine\Common\Util\Inflector;
use Doctrine\ODM\OrientDB\Caster\ReverseCaster;
use Doctrine\ODM\OrientDB\UnitOfWork;
use Doctrine\OrientDB\Binding\HttpBindingInterface;

/**
 * Class DocumentPersister
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class SQLBatchPersister implements PersisterInterface
{
    protected $metadataFactory;
    protected $caster;
    protected $binding;
    protected $inflector;
    protected $queryDocumentMap = array();

    public function __construct(UnitOfWork $uow, Inflector $inflector)
    {
        $manager = $uow->getManager();
        $this->metadataFactory = $manager->getMetadataFactory();
        $this->binding = $manager->getBinding();
        $this->inflector = $inflector;
        $this->caster = new ReverseCaster();
    }


    /**
     * Processes the changeSet and maps the RIDs back to new documents
     * so it can be used in userland.
     *
     * @param ChangeSet $changeSet
     *
     * @throws \Exception
     */
    public function process(ChangeSet $changeSet)
    {
        $queryWriter = new QueryWriter();
        foreach ($changeSet->getInserts() as $identifier => $insert) {
            $metadata = $this->metadataFactory->getMetadataFor(get_class($insert['document']));
            $fields = $this->getCastedFields($insert['changes']);
            $position = $queryWriter->addInsertQuery($identifier, $metadata->getOrientClass(), $fields);
            $this->queryDocumentMap[$position] = array('document' => $insert['document'], 'metadata' => $metadata);
        }

        if ($this->binding instanceof HttpBindingInterface) {
            $batch = array(
                'transaction' => true,
                'operations' => array(
                    array(
                        'type' => 'script',
                        'language' => 'sql',
                        'script' => $queryWriter->getQueries()
                    )
                )
            );
            $results = $this->binding->batch(json_encode($batch))->getData()->result;


        } else {
            throw new \Exception('Only HttpBindingInterface is supported.');
        }

        // set the RID on the created documents
        foreach ($results as $position => $result) {
            $map = $this->queryDocumentMap[$position];
            $document = $map['document'];
            $metadata = $map['metadata'];

            $metadata->setDocumentValue($document, $metadata->getRidPropertyName(), $result->{'@rid'});
        }
    }


    /**
     * Casts the fields and returns an array mapped fieldname => value
     *
     * @param array $changes
     *
     * @return array
     */
    protected function getCastedFields(array $changes)
    {
        $castedChanges = array();
        foreach ($changes as $change) {
            $castedChanges[$change['field']] = $this->castProperty($change['annotation'], $change['value']);
        }

        return $castedChanges;
    }

    /**
     * Casts a value according to how it was annotated.
     *
     * @param  \Doctrine\ODM\OrientDB\Mapper\Annotations\Property  $annotation
     * @param  mixed                                               $propertyValue
     * @return mixed
     */
    protected function castProperty($annotation, $propertyValue)
    {
        $method = 'cast' . $this->inflector->camelize($annotation->type);

        $this->caster->setValue($propertyValue);
        $this->caster->setProperty('annotation', $annotation);

        return $this->caster->$method();
    }

}