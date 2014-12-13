<?php

namespace Doctrine\ODM\OrientDB\Persistence;


interface PersisterInterface
{
    /**
     * Processes the changeSet and maps the RIDs back to new documents
     * so it can be used in userland.
     *
     * @param ChangeSet $changeSet
     *
     */
    public function process(ChangeSet $changeSet);
} 