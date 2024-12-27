<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class PrimeIVLocationsAcfData {

    public const REGION = 'primeiv_location_page_region';
    public const SEVERING_AREAS = 'serving_areas';
    public const REGION_FIELD_ID = 'field_64197f5755eb2';

    public function __construct() {
        $this->init();
    }

    private function init() {
        add_action( 'acf/init', [ $this, 'register_acf_fields' ] );
    }

    public function register_acf_fields() {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }
        $this->new_clean_ready_for_extending();
        $this->old_location_packages();
        
    }

    private function new_clean_ready_for_extending() {
        acf_add_local_field_group( array(
            'key' => 'group_64197f5432d80',
            'title' => 'Location Pages (Site Rebuild)',
            'fields' => array(
                array(
                    'key' => self::REGION_FIELD_ID,
                    'label' => 'Region',
                    'name' => self::REGION,
                    'aria-label' => '',
                    'type' => 'radio',
                    'instructions' => 'Is used to show all locations separated by region on the main Locations page',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'west_coast' => 'West Coast',
                        'mountain' => 'Mountain',
                        'central' => 'Central',
                        'east_coast' => 'East Coast',
                    ),
                    'default_value' => '',
                    'return_format' => 'value',
                    'allow_null' => 1,
                    'other_choice' => 0,
                    'layout' => 'horizontal',
                    'save_other_choice' => 0,
                ),
                array(
                    'key' => 'group_64197f5432d81',
                    'label' => 'Serving Areas',
                    'name' => self::SEVERING_AREAS,
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                )
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ),
                    array(
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => 'location-page.php',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ) );
    }


    private function old_location_packages() {
        acf_add_local_field_group(array(
            'key' => 'group_636952e782640',
            'title' => 'Location Packages',
            'fields' => array(
                array(
                    'key' => 'field_636952e79930f',
                    'label' => 'Vitamin C Infusions',
                    'name' => '',
                    'aria-label' => '',
                    'type' => 'accordion',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'open' => 0,
                    'multi_expand' => 0,
                    'endpoint' => 0,
                ),
                array(
                    'key' => 'field_64253dba37159',
                    'label' => 'Disabled',
                    'name' => 'location_vci_package_disabled',
                    'aria-label' => '',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'message' => '',
                    'default_value' => 0,
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                ),
                array(
                    'key' => 'field_636952e799357',
                    'label' => 'Package Title',
                    'name' => 'location_vci_package_title',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Vitamin C Infusions',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_636952e79939d',
                    'label' => 'Package Features',
                    'name' => 'location_vci_package_features',
                    'aria-label' => '',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '<ul>
<li>5 doses of 12,000 MG - $420</li>
<li>5 doses of 25,000 MG - $775</li>
<li>5 doses of 50,000 MG - $995</li>
<li>6 doses of 50,0asdasd00 MG - $995</li>
</ul>',
                    'maxlength' => '',
                    'rows' => '',
                    'placeholder' => '',
                    'new_lines' => '',
                ),
                array(
                    'key' => 'field_636952e7993e3',
                    'label' => 'Package Description',
                    'name' => 'location_vci_package_description',
                    'aria-label' => '',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Studies have shown that HIGH doses of vitamin C may have the following benefits:
<ul>
 	<li>Reduce symptoms of chronic disease</li>
 	<li>Higher production of collagen</li>
 	<li>Keeping your immune system performing at its maximum potential</li>
 	<li>Better absorption of iron</li>
</ul>',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ),
                array(
                    'key' => 'field_636952e799422',
                    'label' => 'Package Notes',
                    'name' => 'location_vci_package_notes',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'These doses are too high to take orally but are safe <br>intravenously.',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_636952e79945f',
                    'label' => 'Glutathione Beauty Boost IV Pushes',
                    'name' => '',
                    'aria-label' => '',
                    'type' => 'accordion',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'open' => 0,
                    'multi_expand' => 0,
                    'endpoint' => 0,
                ),
                array(
                    'key' => 'field_64253da137158',
                    'label' => 'Disabled',
                    'name' => 'location_gbb_package_disabled',
                    'aria-label' => '',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'message' => '',
                    'default_value' => 0,
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                ),
                array(
                    'key' => 'field_636952e79949c',
                    'label' => 'Package Title',
                    'name' => 'location_gbb_package_title',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Glutathione Beauty Boost IV Pushes',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_636952e7994da',
                    'label' => 'Package Features',
                    'name' => 'location_gbb_package_features',
                    'aria-label' => '',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '<ul><li>10 doses of glutathione - $495</li></ul>',
                    'maxlength' => '',
                    'rows' => '',
                    'placeholder' => '',
                    'new_lines' => '',
                ),
                array(
                    'key' => 'field_636952e799517',
                    'label' => 'Package Description',
                    'name' => 'location_gbb_package_description',
                    'aria-label' => '',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'In our early 40s, our body stops producing enough glutathione to keep our skin looking youthful. Glutathione in high doses has been shown to brighten your skin tone and reduce fine lines, wrinkles, and sun spots. High dose glutathione can also be an amazing overall detox for your body.
<ul>
 	<li>Glow-booster for skin</li>
 	<li>Reduces hyper-pigmentation</li>
 	<li>Promotes cellular repair</li>
 	<li>Flushes out toxins &amp; free radicals</li>
</ul>',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ),
                array(
                    'key' => 'field_636952e799553',
                    'label' => 'Package Notes',
                    'name' => 'location_gbb_package_notes',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Try our most-popular “fountain of youth” boost for <br>radiant skin.',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_636952e799590',
                    'label' => 'NAD+ Infusions',
                    'name' => '',
                    'aria-label' => '',
                    'type' => 'accordion',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'open' => 0,
                    'multi_expand' => 0,
                    'endpoint' => 0,
                ),
                array(
                    'key' => 'field_64253d8a37157',
                    'label' => 'Disabled',
                    'name' => 'location_ni_package_disabled',
                    'aria-label' => '',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'message' => '',
                    'default_value' => 0,
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                ),
                array(
                    'key' => 'field_636952e7995cc',
                    'label' => 'Package Title',
                    'name' => 'location_ni_package_title',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'NAD+ Infusions',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_636952e799609',
                    'label' => 'Package Features',
                    'name' => 'location_ni_package_features',
                    'aria-label' => '',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '<ul><li>4 doses of 500 MG - $1600</li><li>4 doses of 1000 MG - $2200</li></ul>',
                    'maxlength' => '',
                    'rows' => '',
                    'placeholder' => '',
                    'new_lines' => '',
                ),
                array(
                    'key' => 'field_636952e799645',
                    'label' => 'Package Description',
                    'name' => 'location_ni_package_description',
                    'aria-label' => '',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'NAD+ IV therapy is used to dramatically slow the progression of aging, leading to a healthier and younger-looking you. Studies show that chronic stress, anxiety, and depression can cause your NAD+ levels in your body to prematurely deplete, causing sleep disturbances, cognitive impairments, and worsening mood problems.
<ul>
 	<li>Improve focus &amp; mental clarity</li>
 	<li>Protect against degenerative disease</li>
 	<li>Alternative to addiction treatment</li>
 	<li>Avoid medication side-effects</li>
</ul>',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ),
                array(
                    'key' => 'field_636952e799681',
                    'label' => 'Package Notes',
                    'name' => 'location_ni_package_notes',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Studies show that these treatments work <br>best with 4 consecutive infusions.',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_636952e7996be',
                    'label' => 'Weight Loss Package',
                    'name' => '',
                    'aria-label' => '',
                    'type' => 'accordion',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'open' => 0,
                    'multi_expand' => 0,
                    'endpoint' => 0,
                ),
                array(
                    'key' => 'field_64253d4b37156',
                    'label' => 'Disabled',
                    'name' => 'location_wlp_package_disabled',
                    'aria-label' => '',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'message' => '',
                    'default_value' => 0,
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                ),
                array(
                    'key' => 'field_636952e7996fb',
                    'label' => 'Package Title',
                    'name' => 'location_wlp_package_title',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Weight Loss Package',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_636952e799737',
                    'label' => 'Package Features',
                    'name' => 'location_wlp_package_features',
                    'aria-label' => '',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '<ul>
<li>3 cryotherapy sessions</li>
<li>3 weight loss IV drips</li>
</ul>',
                    'maxlength' => '',
                    'rows' => '',
                    'placeholder' => '',
                    'new_lines' => '',
                ),
                array(
                    'key' => 'field_636952e799774',
                    'label' => 'Package Description',
                    'name' => 'location_wlp_package_description',
                    'aria-label' => '',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Studies have shown that by the age of 30, your metabolism slows down drastically. This 2-punch treatment uses cryotherapy to send cold signals to your body that burn calories at a lightning-fast rate. The fat-blasting Skinny IV drip breaks down fat stores to trigger weight loss, all while increasing energy.
<ul>
 	<li>Burn 300-500 calories in five hours</li>
 	<li>Optimize vitamin levels</li>
 	<li>Helps suppress appetite</li>
 	<li>Increase lean muscle mass</li>
</ul>',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ),
                array(
                    'key' => 'field_636952e7997b0',
                    'label' => 'Package Notes',
                    'name' => 'location_wlp_package_notes',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Give our maximum weight loss assistance <br>treatment a go!',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_636952e7997ec',
                    'label' => 'Multi-Injection Packs',
                    'name' => '',
                    'aria-label' => '',
                    'type' => 'accordion',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'open' => 0,
                    'multi_expand' => 0,
                    'endpoint' => 0,
                ),
                array(
                    'key' => 'field_64253cf01239a',
                    'label' => 'Disabled',
                    'name' => 'location_mip_package_disabled',
                    'aria-label' => '',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'message' => '',
                    'default_value' => 0,
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                ),
                array(
                    'key' => 'field_636952e799829',
                    'label' => 'Package Title',
                    'name' => 'location_mip_package_title',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Multi-Injection Packs',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_636952e799866',
                    'label' => 'Package Features',
                    'name' => 'location_mip_package_features',
                    'aria-label' => '',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '<ul>
<li>Any 4 injections - $108</li>
<li>Any 8 injections - $200</li>
<li>Any 12 injections - $250</li>
</ul>',
                    'maxlength' => '',
                    'rows' => '',
                    'placeholder' => '',
                    'new_lines' => '',
                ),
                array(
                    'key' => 'field_636952e7998a2',
                    'label' => 'Package Description',
                    'name' => 'location_mip_package_description',
                    'aria-label' => '',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Looking to save money and make your own customized treatment bundle? Perhaps you need a metabolism, weight loss, energy, and hangover IV drip treatment! Whatever your body needs, we have you covered. Plus our Multiple-Injection pack is more affordable than buying the same in single drips.
<ul>
 	<li>Feel the results immediately</li>
 	<li>Focus on your wellness goals</li>
 	<li>100% absorption &amp; complete rehydration</li>
 	<li>Target nutrition deficiencies</li>
</ul>',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ),
                array(
                    'key' => 'field_636952e7998e3',
                    'label' => 'Package Notes',
                    'name' => 'location_mip_package_notes',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Reduce costs & design a custom pack fit for you.',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => 'location-page.php',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'left',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ));
    }
}

$PrimeIVLocationsAcfData = new PrimeIVLocationsAcfData();
