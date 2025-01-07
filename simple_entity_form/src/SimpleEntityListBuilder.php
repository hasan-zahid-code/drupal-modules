<?php

namespace Drupal\simple_entity_form;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides a listing of Simple Entity entities.
 */
class SimpleEntityListBuilder extends ConfigEntityListBuilder
{
    /**
     * {@inheritdoc}
     */
    public function buildHeader()
    {
        $header['label'] = $this->t('Label');
        $header['id'] = $this->t('Machine name');
        return $header + parent::buildHeader();
    }

    /**
     * {@inheritdoc}
     */
    public function buildRow(EntityInterface $entity)
    {
        $row['label'] = $entity->label();
        $row['id'] = $entity->id();
        return $row + parent::buildRow($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        // Add "Add Simple Entity" button at the top of the list.
        $add_button = Link::fromTextAndUrl($this->t('Add Simple Entity'), Url::fromRoute('simple_entity_form.simple_entity_add'))
            ->toRenderable();
        $add_button['#attributes'] = ['class' => ['button', 'button--primary']];

        // Render the button
        $add_button_rendered = \Drupal::service('renderer')->render($add_button);

        // Get the parent render array and add the button as the first element.
        $build = parent::build();
        // Attach the rendered button at the beginning.
        $build['#attached']['html_header'][] = $add_button_rendered;

        return $build;
    }
}
