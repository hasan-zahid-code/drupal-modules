<?php

namespace Drupal\etss2_theme_settings\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Theme settings configuration form with file upload.
 */
class ThemeSettingsForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return ['etss2_theme_settings.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'etss2_theme_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('etss2_theme_settings.settings');

    // Font file upload fields.
    $font_fields = [
      'font_large_text_bold' => 'Font Large Text - Bold',
      'font_large_text_light' => 'Font Large Text - Light',
      'font_body_bold' => 'Font Body - Bold',
      'font_body_light' => 'Font Body - Light',
      'font_heading_bold' => 'Font Heading - Bold',
      'font_heading_light' => 'Font Heading - Light',
    ];

    $form['font_settings'] = [
    '#type' => 'details',
    '#title' => $this->t('Font Settings'),
    ];
    foreach ($font_fields as $field_name => $label) {
      $form['font_settings'][$field_name] = [
        '#type' => 'managed_file',
        '#title' => $this->t($label),
        '#upload_location' => 'public://fonts/',
        '#default_value' => $config->get($field_name) ?? [],
        '#upload_validators' => [
          'file_validate_extensions' => ['otf ttf woff woff2']
        ],
      ];
    }

    $form['color_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Color Settings'),
    ];

    $form['color_settings']['primary'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Primary Colors'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['color_settings']['primary']['primary_background'] = [
      '#type' => 'color',
      '#title' => $this->t('Primary Background Color'),
      '#default_value' => $config->get('primary_background') ?? '#ffffff',
      '#description' => $this->t('The primary background color for your site. Example: White (#ffffff).'),
    ];

    $form['color_settings']['primary']['primary_accent'] = [
      '#type' => 'color',
      '#title' => $this->t('Primary Accent Color'),
      '#default_value' => $config->get('primary_accent') ?? '#1f87d6',
      '#description' => $this->t('The accent color for primary elements. Example: Blue (#1f87d6).'),
    ];

    $form['color_settings']['secondary'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Secondary Colors'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];

    $form['color_settings']['secondary']['secondary_background'] = [
      '#type' => 'color',
      '#title' => $this->t('Secondary Background Color'),
      '#default_value' => $config->get('secondary_background') ?? '#3a3636',
      '#description' => $this->t('Secondary background color for header/footer. Example: Dark Gray (#3a3636).'),
    ];

    $form['color_settings']['secondary']['secondary_accent'] = [
      '#type' => 'color',
      '#title' => $this->t('Secondary Accent Color'),
      '#default_value' => $config->get('secondary_accent') ?? '#333333',
      '#description' => $this->t('Secondary accent color for header/footer. Example: Gray (#333333).'),
    ];

    $form['color_settings']['alternate'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Alternate Colors'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    
    $form['color_settings']['tertiary'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Tertiary Colors'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];

    $form['color_settings']['tertiary']['tertiary_background'] = [
      '#type' => 'color',
      '#title' => $this->t('Tertiary Background Color'),
      '#default_value' => $config->get('tertiary_background') ?? '#e0e0e0',
      '#description' => $this->t('Tertiary background color for less frequently used layouts such as sidebars. Example: Light Gray (#e0e0e0).'),
    ];

    $form['color_settings']['tertiary']['tertiary_accent'] = [
      '#type' => 'color',
      '#title' => $this->t('Tertiary Accent Color'),
      '#default_value' => $config->get('tertiary_accent') ?? '#666666',
      '#description' => $this->t('Accent color for tertiary elements like side navigation links or subtle section dividers. Example: Light Gray (#666666).'),
    ];

    $form['color_settings']['alternate']['alternate_background'] = [
      '#type' => 'color',
      '#title' => $this->t('Alternate Background Color'),
      '#default_value' => $config->get('alternate_background') ?? '#f9f9f9',
      '#description' => $this->t('Background color for alternating sections. Example: Light Gray (#f9f9f9).'),
    ];

    $form['color_settings']['alternate']['alternate_accent'] = [
      '#type' => 'color',
      '#title' => $this->t('Alternate Accent Color'),
      '#default_value' => $config->get('alternate_accent') ?? '#cccccc',
      '#description' => $this->t('Background accent color for alternating sections. Example: Light Gray Accent (#cccccc).'),
    ];

    $form['color_settings']['interactive'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Interactive Colors'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];

    $form['color_settings']['interactive']['interactive_hover'] = [
      '#type' => 'color',
      '#title' => $this->t('Hover Color'),
      '#default_value' => $config->get('interactive_hover') ?? '#b3d7ff',
      '#description' => $this->t('Color when elements are hovered over. Example: Light Blue (#b3d7ff).'),
    ];

    $form['color_settings']['interactive']['interactive_active'] = [
      '#type' => 'color',
      '#title' => $this->t('Active Color'),
      '#default_value' => $config->get('interactive_active') ?? '#0066cc',
      '#description' => $this->t('Color when elements are actively clicked or selected. Example: Blue (#0066cc).'),
    ];

    $form['color_settings']['interactive']['interactive_disabled'] = [
      '#type' => 'color',
      '#title' => $this->t('Disabled Color'),
      '#default_value' => $config->get('interactive_disabled') ?? '#d1d1d1',
      '#description' => $this->t('Color for disabled elements. Example: Light Gray (#d1d1d1).'),
    ];

    $form['color_settings']['feedback'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Feedback Colors'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];

    $form['color_settings']['feedback']['feedback_error'] = [
      '#type' => 'color',
      '#title' => $this->t('Error Color'),
      '#default_value' => $config->get('feedback_error') ?? '#ff0000',
      '#description' => $this->t('Used for error messages. Example: Red (#ff0000).'),
    ];

    $form['color_settings']['feedback']['feedback_error_light'] = [
      '#type' => 'color',
      '#title' => $this->t('Error Color (Light)'),
      '#default_value' => $config->get('feedback_error_light') ?? '#f8d7da',
      '#description' => $this->t('Light version of error messages. Example: Light Red (#f8d7da).'),
    ];

    $form['color_settings']['feedback']['feedback_warning'] = [
      '#type' => 'color',
      '#title' => $this->t('Warning Color'),
      '#default_value' => $config->get('feedback_warning') ?? '#ffaa00',
      '#description' => $this->t('Used for warning messages. Example: Orange (#ffaa00).'),
    ];

    $form['color_settings']['feedback']['feedback_warning_light'] = [
      '#type' => 'color',
      '#title' => $this->t('Warning Color (Light)'),
      '#default_value' => $config->get('feedback_warning_light') ?? '#ffe89e',
      '#description' => $this->t('Light version of warning messages. Example: Light Orange (#ffe89e).'),
    ];

    $form['color_settings']['feedback']['feedback_success'] = [
      '#type' => 'color',
      '#title' => $this->t('Success Color'),
      '#default_value' => $config->get('feedback_success') ?? '#00ff00',
      '#description' => $this->t('Used for success messages. Example: Green (#00ff00).'),
    ];

    $form['color_settings']['feedback']['feedback_success_light'] = [
      '#type' => 'color',
      '#title' => $this->t('Success Color (Light)'),
      '#default_value' => $config->get('feedback_success_light') ?? '#c2ffd0',
      '#description' => $this->t('Light version of success messages. Example: Light Green (#c2ffd0).'),
    ];

    $form['color_settings']['feedback']['feedback_info'] = [
      '#type' => 'color',
      '#title' => $this->t('Info Color'),
      '#default_value' => $config->get('feedback_info') ?? '#17a2b8',
      '#description' => $this->t('Used for informational messages. Example: Blue (#17a2b8).'),
    ];

    $form['color_settings']['feedback']['feedback_info_light'] = [
      '#type' => 'color',
      '#title' => $this->t('Info Color (Light)'),
      '#default_value' => $config->get('feedback_info_light') ?? '#c7f7ff',
      '#description' => $this->t('Light version of informational messages. Example: Light Blue (#c7f7ff).'),
    ];

    $form['color_settings']['typography'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Typography Colors'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];

    $form['color_settings']['typography']['text_dark'] = [
      '#type' => 'color',
      '#title' => $this->t('Text Dark Color'),
      '#default_value' => $config->get('text_dark') ?? '#000000',
      '#description' => $this->t('The default color for text in the body of the website. Example: Dark Text (#000000).'),
    ];

    $form['color_settings']['typography']['text_light'] = [
      '#type' => 'color',
      '#title' => $this->t('Text Light Color'),
      '#default_value' => $config->get('text_light') ?? '#ffffff',
      '#description' => $this->t('The color used for text on dark backgrounds. Example: Light Text (#ffffff).'),
    ];

    $form['color_settings']['typography']['text_accent'] = [
      '#type' => 'color',
      '#title' => $this->t('Text Accent Color'),
      '#default_value' => $config->get('text_accent') ?? '#888888',
      '#description' => $this->t('An accent color for text, typically used for less important content or secondary text. Example: Accent Text (#888888).'),
    ];

    $form['layout_spacing'] = [
      '#type' => 'details',
      '#title' => $this->t('Layout and Spacing Settings'),
      '#description' => $this->t('Configure the layout and spacing for the site.'),
    ];
    $form['layout_spacing']['padding'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Global Padding'),
      '#default_value' => $theme_settings->padding ?? '20px',
      '#description' => $this->t('Set the global padding for sections, e.g., 20px. This will be applied consistently across different sections of the site.'),
      '#required' => TRUE,
      '#pattern' => '^\d+(px|em|rem|%)$',
      '#element_validate' => [[get_class(object: $this), 'validateSpacing']],
    ];
    $form['layout_spacing']['margin'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Global Margin'),
      '#default_value' => $theme_settings->margin ?? '20px',
      '#description' => $this->t('Set the global margin for sections, e.g., 20px. This helps control the spacing around different elements.'),
      '#required' => TRUE,
      '#pattern' => '^\d+(px|em|rem|%)$',
      '#element_validate' => [[get_class(object: $this), 'validateSpacing']],
    ];
    $form['layout_spacing']['grid_columns'] = [
      '#type' => 'select',
      '#title' => $this->t('Grid Layout Columns'),
      '#options' => [
        '2' => $this->t('2 Columns'),
        '3' => $this->t('3 Columns'),
        '4' => $this->t('4 Columns'),
      ],
      '#default_value' => $theme_settings->grid_columns ?? '3',
      '#description' => $this->t('Select the number of columns for the grid layout. This will determine how content is arranged in different sections of the site.'),
      '#required' => TRUE,
    ];

    // Custom CSS/JavaScript Editor with validation.
    $form['custom_code'] = [
      '#type' => 'details',
      '#title' => $this->t('Custom CSS/JavaScript'),
      '#description' => $this->t('Add custom CSS and JavaScript for advanced customization. Ensure that the syntax is correct.'),
    ];
    $form['custom_code']['custom_css'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Custom CSS'),
      '#default_value' => $theme_settings->custom_css ?? '',
      '#description' => $this->t('Add any custom CSS that should be applied globally. This can be used for minor adjustments not covered by the theme settings. Example: "body {background-color: #ffffff;}"'),
      '#attributes' => [
        'class' => ['custom-css-editor'],
        'placeholder' => $this->t('/* Write your CSS here */'),
      ],
    ];
    $form['custom_code']['custom_js'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Custom JavaScript'),
      '#default_value' => $theme_settings->custom_js ?? '',
      '#description' => $this->t('Add any custom JavaScript that should be applied globally. Be cautious with your scripts to avoid conflicts.'),
      '#attributes' => [
        'class' => ['custom-js-editor'],
        'placeholder' => $this->t('// Write your JavaScript here'),
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  public static function validateSpacing(&$element, FormStateInterface $form_state, &$form)
  {
    $value = $form_state->getValue($element['#name']);
    if (!preg_match('/^\d+(px|em|rem|%)$/', subject: $value)) {
      $form_state->setError($element, t('The value must be a valid CSS spacing format (e.g., 20px, 1em, 50%).'));
    }
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {

    // Fetch the configuration for theme settings and S3.
    $config = $this->config('etss2_theme_settings.settings'); // For theme settings
    $s3_config = \Drupal::config('s3fs.settings'); // For S3 configuration

    // // Convert the configuration object to a JSON string
    // $s3_config_json = json_encode($s3_config->getRawData(), JSON_PRETTY_PRINT);

    // $this->messenger()->addMessage($this->t('S3 Configuration: <pre>@x</pre>', ['@x' => $s3_config_json]));


    // Get the bucket name from the S3 configuration.
    $bucket_name = $s3_config->get('bucket');
    $public_folder = $s3_config->get('public_folder');

    // Define the font fields you want to process.
    $font_fields = [
      'font_large_text_bold',
      'font_large_text_light',
      'font_body_bold',
      'font_body_light',
      'font_heading_bold',
      'font_heading_light',
    ];

    // Loop through the font fields and process them.
    foreach ($font_fields as $field_name) {
      $file_ids = $form_state->getValue($field_name);

      if (!empty($file_ids)) {
        // Load the first file ID in the array (assuming a single file is uploaded)
        $file = File::load(reset($file_ids));
        if ($file) {
          // Set the file as permanent
          $file->setPermanent();
          $file->save();

          // Get the file URI and construct the S3 URL
          $file_uri = $file->getFileUri();
          $file_url = 'https://' . $bucket_name . '.s3.amazonaws.com/' . $public_folder . '/' . ltrim($file_uri, 'public://');

          // Save the file ID and file URL in the configuration
          $config->set($field_name, $file_ids)
            ->set($field_name . '_url', $file_url)
            ->save();
        }
      } else {
        // If no file is uploaded, clear the configuration for that field
        $config->clear($field_name);
      }
    }



    // Save color settings.
    $config
      ->set('primary_background', $form_state->getValue('primary_background'))
      ->set('primary_accent', $form_state->getValue('primary_accent'))
      ->set('secondary_background', $form_state->getValue('secondary_background'))
      ->set('secondary_accent', $form_state->getValue('secondary_accent'))
      ->set('tertiary_background', $form_state->getValue('tertiary_background'))
      ->set('tertiary_accent', $form_state->getValue('tertiary_accent'))
      ->set('alternate_background', $form_state->getValue('alternate_background'))
      ->set('alternate_accent', $form_state->getValue('alternate_accent'))
      ->set('interactive_hover', $form_state->getValue('interactive_hover'))
      ->set('interactive_active', $form_state->getValue('interactive_active'))
      ->set('interactive_disabled', $form_state->getValue('interactive_disabled'))
      ->set('text_dark', $form_state->getValue('text_dark'))
      ->set('text_light', $form_state->getValue('text_light'))
      ->set('text_accent', $form_state->getValue('text_accent'))
      ->set('feedback_error', $form_state->getValue('feedback_error'))
      ->set('feedback_error_light', $form_state->getValue('feedback_error_light'))
      ->set('feedback_warning', $form_state->getValue('feedback_warning'))
      ->set('feedback_warning_light', $form_state->getValue('feedback_warning_light'))
      ->set('feedback_success', $form_state->getValue('feedback_success'))
      ->set('feedback_success_light', $form_state->getValue('feedback_success_light'))
      ->set('feedback_info', $form_state->getValue('feedback_info'))
      ->set('feedback_info_light', $form_state->getValue('feedback_info_light'))
      ->set('highlight', $form_state->getValue('highlight'))
      ->set('highlight_light', $form_state->getValue('highlight_light'))
      ->set('overlay', $form_state->getValue('overlay'))
      ->set('padding', $form_state->getValue('padding'))
      ->set('margin', $form_state->getValue('margin'))
      ->set('grid_columns', $form_state->getValue('grid_columns'))
      ->set('custom_css', $form_state->getValue('custom_css'))
      ->set('custom_js', $form_state->getValue('custom_js'))
      ->save();


    parent::submitForm($form, $form_state);
  }


}
