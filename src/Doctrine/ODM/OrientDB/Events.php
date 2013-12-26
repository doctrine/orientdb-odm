<?php

namespace Doctrine\ODM\OrientDB;

/**
 * Container for all ODM events.
 *
 * This class cannot be instantiated.
 */
final class Events
{
    private function __construct() {}

    /**
     * The preRemove event occurs for a given document before the respective
     * Manager remove operation for that document is executed.
     *
     * This is an document lifecycle event.
     *
     * @var string
     */
    const preRemove = 'preRemove';

    /**
     * The postRemove event occurs for an document after the document has
     * been deleted. It will be invoked after the database delete operations.
     *
     * This is an document lifecycle event.
     *
     * @var string
     */
    const postRemove = 'postRemove';

    /**
     * The prePersist event occurs for a given document before the respective
     * Manager persist operation for that document is executed.
     *
     * This is an document lifecycle event.
     *
     * @var string
     */
    const prePersist = 'prePersist';

    /**
     * The postPersist event occurs for an document after the document has
     * been made persistent. It will be invoked after the database insert operations.
     * Generated primary key values are available in the postPersist event.
     *
     * This is an document lifecycle event.
     *
     * @var string
     */
    const postPersist = 'postPersist';

    /**
     * The preUpdate event occurs before the database update operations to
     * document data.
     *
     * This is an document lifecycle event.
     *
     * @var string
     */
    const preUpdate = 'preUpdate';

    /**
     * The postUpdate event occurs after the database update operations to
     * document data.
     *
     * This is an document lifecycle event.
     *
     * @var string
     */
    const postUpdate = 'postUpdate';

    /**
     * The preLoad event occurs for a document before the document has been loaded
     * into the current Manager from the database or before the refresh operation
     * has been applied to it.
     *
     * This is a document lifecycle event.
     *
     * @var string
     */
    const preLoad = 'preLoad';

    /**
     * The postLoad event occurs for a document after the document has been loaded
     * into the current Manager from the database or after the refresh operation
     * has been applied to it.
     *
     * Note that the postLoad event occurs for an document before any associations have been
     * initialized. Therefore it is not safe to access associations in a postLoad callback
     * or event handler.
     *
     * This is a document lifecycle event.
     *
     * @var string
     */
    const postLoad = 'postLoad';

    /**
     * The loadClassMetadata event occurs after the mapping metadata for a class
     * has been loaded from a mapping source (annotations/xml/yaml).
     *
     * @var string
     */
    const loadClassMetadata = 'loadClassMetadata';

    /**
     * The preFlush event occurs when the Manager#flush() operation is invoked,
     * but before any changes to managed documents have been calculated. This event is
     * always raised right after Manager#flush() call.
     */
    const preFlush = 'preFlush';

    /**
     * The onFlush event occurs when the Manager#flush() operation is invoked,
     * after any changes to managed documents have been determined but before any
     * actual database operations are executed. The event is only raised if there is
     * actually something to do for the underlying UnitOfWork. If nothing needs to be done,
     * the onFlush event is not raised.
     *
     * @var string
     */
    const onFlush = 'onFlush';

    /**
     * The postFlush event occurs when the Manager#flush() operation is invoked and
     * after all actual database operations are executed successfully. The event is only raised if there is
     * actually something to do for the underlying UnitOfWork. If nothing needs to be done,
     * the postFlush event is not raised. The event won't be raised if an error occurs during the
     * flush operation.
     *
     * @var string
     */
    const postFlush = 'postFlush';

    /**
     * The onClear event occurs when the Manager#clear() operation is invoked,
     * after all references to documents have been removed from the unit of work.
     *
     * @var string
     */
    const onClear = 'onClear';
}
