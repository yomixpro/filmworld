<?php
namespace FluentFormPro\Components;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use FluentForm\App\Helpers\Helper;
use FluentForm\App\Services\FormBuilder\BaseFieldManager;
use FluentForm\Framework\Helpers\ArrayHelper;

class RepeaterField extends BaseFieldManager
{
    /**
     * Wrapper class for repeat element
     * @var string
     */
    protected $wrapperClass = 'ff-el-repeater js-repeater';

    public function __construct()
    {
        parent::__construct(
            'repeater_field',
            'Repeater Field',
            ['repeat', 'list', 'multiple'],
            'advanced'
        );

        add_filter('fluentform/response_render_repeater_field', array($this, 'renderResponse'), 10, 3);

    }

    function getComponent()
    {
        return [
            'index' => 16,
            'element' => 'repeater_field',
            'attributes' => array(
                'name' => 'repeater_field',
                'data-type' => 'repeater_field'
            ),
            'settings' => array(
                'label' => __('Repeater Field', 'fluentformpro'),
                'admin_field_label' => '',
                'container_class' => '',
                'label_placement' => '',
                'validation_rules' => array(),
                'conditional_logics' => array(),
                'max_repeat_field' => ''
            ),
            'fields' => array(
                array(
                    'element' => 'input_text',
                    'attributes' => array(
                        'type' => 'text',
                        'value' => '',
                        'placeholder' => '',
                    ),
                    'settings' => array(
                        'label' => __('Column 1', 'fluentformpro'),
                        'help_message' => '',
                        'validation_rules' => array(
                            'required' => array(
                                'value'          => false,
                                'global'         => true,
                                'message'        => Helper::getGlobalDefaultMessage('required'),
                                'global_message' => Helper::getGlobalDefaultMessage('required'),
                            )
                        )
                    ),
                    'editor_options' => array(),
                )
            ),
            'editor_options' => array(
                'title' => __('Repeat Field', 'fluentformpro'),
                'icon_class' => 'ff-edit-repeat',
                'template' => 'fieldsRepeatSettings'
            ),
        ];
    }

    public function getGeneralEditorElements()
    {
        return [
            'label',
            'label_placement',
            'admin_field_label',
            'fields_repeat_settings',
        ];
    }

    public function getAdvancedEditorElements()
    {
        return [
            'container_class',
            'name',
            'conditional_logics',
            'max_repeat_field'
        ];
    }

    public function getEditorCustomizationSettings()
    {
        return [
            'fields_repeat_settings' => array(
                'template' => 'fieldsRepeatSettings',
                'label' => __('Repeat Field Columns', 'fluentformpro'),
                'help_text' => __('Field Columns which a user will be able to repeat.', 'fluentformpro'),
            )
        ];
    }

    /**
     * Compile and echo the html element
     * @param array $data [element data]
     * @param stdClass $form [Form Object]
     * @return void
     */
    public function render($data, $form)
    {
        $elementName = $data['element'];
        $data = apply_filters_deprecated(
            'fluentform_rendering_field_data_' . $elementName,
            [
                $data,
                $form
            ],
            FLUENTFORM_FRAMEWORK_UPGRADE,
            'fluentform/rendering_field_data_' . $elementName,
            'Use fluentform/rendering_field_data_' . $elementName . ' instead of fluentform_rendering_field_data_' . $elementName
        );
        $data = apply_filters('fluentform/rendering_field_data_' . $elementName, $data, $form);

        // Test implementation using address component
        $rootName = $data['attributes']['name'];
        $hasConditions = $this->hasConditions($data) ? 'has-conditions ' : '';
        @$data['attributes']['class'] .= ' ff-el-group ' . $this->wrapperClass . ' ' . $hasConditions . ' ' . ArrayHelper::get($data, 'settings.container_class');
        $data['attributes']['class'] = trim($data['attributes']['class']);

        $maxRepeat = ArrayHelper::get($data, 'settings.max_repeat_field');
        $data['attributes']['data-max_repeat'] = $maxRepeat;
        $data['attributes']['data-root_name'] = $rootName;

        if ($labelPlacement = ArrayHelper::get($data, 'settings.label_placement')) {
            $data['attributes']['class'] .= ' ff-el-form-' . $labelPlacement;
        }

        $atts = $this->buildAttributes(
            \FluentForm\Framework\Helpers\ArrayHelper::except($data['attributes'], 'name')
        );

        $fields = $data['fields'];

        ob_start();
        ?>
        <div <?php echo $atts; ?> >
            <?php echo $this->buildElementLabel($data, $form); ?>
            <div class='ff-el-input--content'>
                <table role="table" aria-label="Repeater Field" data-max_repeat="<?php echo $maxRepeat; ?>" data-root_name="<?php echo $rootName; ?>" class="ff_repeater_table ff_flexible_table">
                    <thead>
                    <tr>
                        <?php foreach ($fields as $field): ?>
                            <th><?php echo $this->buildElementLabel($field, $form); ?></th>
                        <?php endforeach; ?>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <?php foreach ($fields as $key => $item):
                            $itemLabel = $item['settings']['label'];
                            $item['settings']['label'] = '';
                            $item['attributes']['aria-label'] = 'repeater level 1 and field ' . $itemLabel;
                            $item['attributes']['name'] = $rootName . '[0][]';
                            $item['attributes']['id'] = $this->makeElementId($data, $form) . '_' . $key;
                            $item['attributes']['data-repeater_index'] = $key;
                            $item['attributes']['data-type'] = 'repeater_item';
                            $item['attributes']['data-name'] = $rootName.'_'.$key.'_0';
                            $item['attributes']['data-error_index'] = $rootName.'['.$key.']';
                            $item['attributes']['data-default'] = ArrayHelper::get($item, 'attributes.value');
                            ?>
                            <td data-label="<?php echo $itemLabel; ?>">
                                <?php $item['element'] = $item['element'] == 'input_mask' ? 'input_text' : $item['element'] ; ?>
                                <?php
                                    do_action_deprecated(
                                        'fluentform_render_item_' . $item['element'],
                                        [
                                            $item,
                                            $form
                                        ],
                                        FLUENTFORM_FRAMEWORK_UPGRADE,
                                        'fluentform/render_item_' . $item['element'],
                                        'Use fluentform/render_item_' . $item['element'] . ' instead of fluentform_render_item_' . $item['element']
                                    );
                                    do_action('fluentform/render_item_' . $item['element'], $item, $form);?>
                            </td>
                        <?php endforeach; ?>
                        <td class="repeat_btn"><?php echo $this->getRepeater($data['element']); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
    
        $html = apply_filters_deprecated(
            'fluentform_rendering_field_html_' . $elementName,
            [
                $html,
                $data,
                $form
            ],
            FLUENTFORM_FRAMEWORK_UPGRADE,
            'fluentform/rendering_field_html_' . $elementName,
            'Use fluentform/rendering_field_html_' . $elementName . ' instead of fluentform_rendering_field_html_' . $elementName
        );

        echo apply_filters('fluentform/rendering_field_html_' . $elementName, $html, $data, $form);
        \FluentForm\App\Helpers\Helper::getNextTabIndex(50);
    }

    /**
     * Compile repeater buttons
     * @param string $el [element name]
     * @return string
     */
    protected function getRepeater($el)
    {
        if ($el == 'repeater_field') {
            $div = '<div class="ff-el-repeat-buttons-list js-repeat-buttons">';
            $div .= '<span role="button" aria-label="add repeater" class="repeat-plus"><svg tabindex="0" width="20" height="20" viewBox="0 0 512 512"><path d="m256 48c-115 0-208 93-208 208 0 115 93 208 208 208 115 0 208-93 208-208 0-115-93-208-208-208z m107 229l-86 0 0 86-42 0 0-86-86 0 0-42 86 0 0-86 42 0 0 86 86 0z"></path></svg></span>';
            $div .= '<span role="button" aria-label="remove repeater" class="repeat-minus"><svg tabindex="0" width="20" height="20" viewBox="0 0 512 512"><path d="m256 48c-115 0-208 93-208 208 0 115 93 208 208 208 115 0 208-93 208-208 0-115-93-208-208-208z m107 229l-214 0 0-42 214 0z""></path></svg></span>';
            $div .= '</div>';
            return $div;
        }
        return '';
    }

    public function renderResponse($response, $field, $form_id)
    {
        if (defined('FLUENTFORM_RENDERING_ENTRIES')) {
            return __('....', 'fluentformpro');
        }

        if (is_string($response) || empty($response) || !is_array($response)) {
            return '';
        }

        $fields = ArrayHelper::get($field, 'raw.fields');
        $columnCount = (count($fields) > count((array)$response[0])) ? count($fields) : count((array)$response[0]);
        $columns = array_fill(0, $columnCount, 'column');

        if (defined('FLUENTFORM_DOING_CSV_EXPORT')) {
            return self::getResponseAsText($response, $fields, $columns);
        }

        return $this->getResponseHtml($response, $fields, $columns);

    }

    protected function getResponseHtml($response, $fields, $columns)
    {
        ob_start();
        ?>
        <div class="ff_entry_table_wrapper">
            <table class="ff_entry_table_field ff-table">
                <thead>
                <tr>
                    <?php foreach ($columns as $index => $count): ?>
                        <th><?php echo ArrayHelper::get($fields, $index . '.settings.label'); ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($response as $responseIndex => $item): ?>
                    <tr>
                        <?php foreach ($columns as $index => $count): ?>
                            <td><?php echo ArrayHelper::get($item, $index); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        $response = ob_get_clean();
        return $response;
    }

    public static function getResponseAsText($response, $fields, $columns = [])
    {
        if (!is_array($response) || !is_array($fields)) {
            return '';
        }
        if (!$columns) {
            $columnCount = (count($fields) > count((array)ArrayHelper::get($response, 0))) ? count($fields) : count((array)ArrayHelper::get($response, 0));
            $columns = array_fill(0, $columnCount, 'column');
        }
        $totalColumns = count($columns);
        $text  = '';
        foreach ($columns as $index => $count) {
            $text .= trim(ArrayHelper::get($fields, $index . '.settings.label', ' '));
            if( $index+1 != $totalColumns ) {
                $text .= " | ";
            }
        }
        $text .= "\n";
        foreach ($response as $responseIndex => $item):
            foreach ($columns as $index => $count):
                $text .= ArrayHelper::get($item, $index);
                if( $index+1 != $totalColumns ) {
                    $text .= " | ";
                }
            endforeach;
            $text .= "\n";
        endforeach;
        return $text;
    }
}
