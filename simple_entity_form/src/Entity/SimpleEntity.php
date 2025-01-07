<?php

namespace Drupal\simple_entity_form\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Simple Entity entity.
 *
 * @ConfigEntityType(
 *   id = "simple_entity",
 *   label = @Translation("Simple Entity"),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\simple_entity_form\SimpleEntityForm",
 *       "edit" = "Drupal\simple_entity_form\SimpleEntityForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "list_builder" = "Drupal\simple_entity_form\SimpleEntityListBuilder",
 *   },
 *   config_prefix = "simple_entity",
 *   admin_permission = "administer simple entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "simple_field"
 *   },
 *   links = {
 *     "collection" = "/admin/structure/simple-entity",
 *     "add-form" = "/admin/structure/simple-entity/add",
 *     "edit-form" = "/admin/structure/simple-entity/{simple_entity}/edit",
 *     "delete-form" = "/admin/structure/simple-entity/{simple_entity}/delete"
 *   }
 * )
 */
class SimpleEntity extends ConfigEntityBase
{
    /**
     * The entity ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The label for the entity.
     *
     * @var string
     */
    protected $label;

    /**
     * A simple field for demonstration.
     *
     * @var string
     */
    protected $simple_field;

    /**
     * Gets the value of the simple field.
     *
     * @return string
     *   The value of the simple field.
     */
    public function getSimpleField()
    {
        return $this->simple_field;
    }

    /**
     * Sets the value of the simple field.
     *
     * @param string $value
     *   The value to set.
     */
    public function setSimpleField($value)
    {
        $this->simple_field = $value;
    }
}
