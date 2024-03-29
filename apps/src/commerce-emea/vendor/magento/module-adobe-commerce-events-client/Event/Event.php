<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeCommerceEventsClient\Event;

/**
 * Event data object
 *
 * @api
 * @since 1.1.0
 */
class Event
{
    public const EVENT_NAME = 'name';
    public const EVENT_PARENT = 'parent';
    public const EVENT_FIELDS = 'fields';
    public const EVENT_RULES = 'rules';
    public const EVENT_PROCESSORS = 'processors';
    public const EVENT_ENABLED = 'enabled';
    public const EVENT_OPTIONAL = 'optional';
    public const EVENT_PRIORITY = 'priority';
    public const EVENT_DESTINATION = 'destination';

    public const DESTINATION_DEFAULT = 'default';

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string|null
     */
    private ?string $parent;

    /**
     * @var boolean
     */
    private bool $optional;

    /**
     * @var boolean
     */
    private bool $enabled;

    /**
     * @var boolean
     */
    private bool $priority;

    /**
     * @var EventField[]
     */
    private array $fields;

    /**
     * @var array
     */
    private array $rules;

    /**
     * @var array
     */
    private array $processors;

    /**
     * @var string
     */
    private string $destination;

    /**
     * @param string $name
     * @param string|null $parent
     * @param bool $optional
     * @param bool $enabled
     * @param bool $priority
     * @param string[] $fields
     * @param array $rules
     * @param array $processors
     * @param string $destination
     */
    public function __construct(
        string $name,
        string $parent = null,
        bool $optional = false,
        bool $enabled = true,
        bool $priority = false,
        array $fields = [],
        array $rules = [],
        array $processors = [],
        string $destination = self::DESTINATION_DEFAULT
    ) {
        $this->name = $name;
        $this->parent = $parent;
        $this->optional = $optional;
        $this->enabled = $enabled;
        $this->priority = $priority;
        $this->fields = $fields;
        $this->rules = $rules;
        $this->processors = $processors;
        $this->destination = $destination;
    }

    /**
     * Returns event name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Checks if event is optional.
     *
     * @return bool
     */
    public function isOptional(): bool
    {
        return $this->optional;
    }

    /**
     * Checks if event is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Checks if event has priority.
     *
     * @return bool
     */
    public function isPriority(): bool
    {
        return $this->priority;
    }

    /**
     * Returns a list of event field names.
     *
     * @return string[]
     */
    public function getFields(): array
    {
        return array_map(fn ($eventField) => $eventField->getName(), $this->getEventFields());
    }

    /**
     * Returns a list of event field objects.
     *
     * @return EventField[]
     */
    public function getEventFields(): array
    {
        return $this->fields;
    }

    /**
     * Returns a list of event rules.
     *
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Returns a list of event processors
     *
     * @return array
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    /**
     * Returns name of parent event.
     *
     * @return string|null
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * Returns event destination.
     *
     * @return string
     */
    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * Checks if current event is based on the provided event code.
     *
     * @param string $eventName
     * @return bool
     */
    public function isBasedOn(string $eventName): bool
    {
        return $eventName == $this->getName() && empty($this->getParent()) || ($eventName == $this->getParent());
    }
}
