<?php

namespace Drupal\simple_entity_form;


use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Simple Entity forms.
 */
class SimpleEntityForm extends EntityForm
{

    /**
     * {@inheritdoc}
     */
    public function form(array $form, FormStateInterface $form_state)
    {
        /** @var \Drupal\simple_entity_form\SimpleEntity $entity */
        $entity = $this->entity;

        $form['label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Label'),
            '#default_value' => $entity->label(),
            '#description' => $this->t('Enter a label for the entity.'),
            '#required' => TRUE,
        ];

        $form['id'] = [
            '#type' => 'machine_name',
            '#default_value' => $entity->id(),
            '#machine_name' => [
                'exists' => '\Drupal\simple_entity_form\Entity\SimpleEntity::load',
            ],
            '#disabled' => !$entity->isNew(),
        ];

        $form['simple_field'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Simple Field'),
            '#default_value' => $entity->get('simple_field'),
            '#description' => $this->t('Enter a value for the simple field.'),
        ];

        return parent::form($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);

        if (strlen($form_state->getValue('simple_field')) > 255) {
            $form_state->setErrorByName('simple_field', $this->t('The value cannot exceed 255 characters.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state)
    {
        $entity = $this->entity;
        $status = $entity->save();

        if ($status == SAVED_NEW) {
            $this->messenger()->addMessage($this->t('The %label entity has been created.', ['%label' => $entity->label()]));
        } else {
            $this->messenger()->addMessage($this->t('The %label entity has been updated.', ['%label' => $entity->label()]));
        }

        $form_state->setRedirectUrl($entity->toUrl('collection'));
    }

}
