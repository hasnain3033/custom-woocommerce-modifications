<?php

use Elementor\Widget_Heading;
use ElementorPro\Base\Base_Widget_Trait;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Custom_Product_Title_Widget extends Widget_Heading {

    use Base_Widget_Trait;

    public function get_name() {
        return 'custom_product_title';
    }

    public function get_title() {
        return __( 'Custom Product Title', 'your-plugin' );
    }

    public function get_icon() {
        return 'eicon-product-title';
    }

	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'title', 'heading', 'product' ];
	}

    protected function register_controls() {
		parent::register_controls();

		$this->update_control(
			'title',
			[
				'dynamic' => [
					'default' => Plugin::elementor()->dynamic_tags->tag_data_to_tag_text( null, 'woocommerce-product-title-tag' ),
				],
			],
			[
				'recursive' => true,
			]
		);

		$this->update_control(
			'header_size',
			[
				'default' => 'h1',
			]
		);
        $this->start_controls_section(
            'section_custom_title',
            [
                'label' => __( 'Custom Title Settings', 'your-plugin' ),
            ]
        );
        $this->add_control(
            'char_limit',
            [
                'label' => __( 'Character Limit', 'your-plugin' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'description' => __( 'Set the maximum number of characters to display. Leave blank or set to 0 for no limit.', 'your-plugin' ),
                'min' => 0,
            ]
        );

        $this->end_controls_section();
	}

    protected function render() {
        $settings = $this->get_settings_for_display();
        $title = $settings['title'];

        // Apply character limit if set
        if (!empty($settings['char_limit']) && $settings['char_limit'] > 0) {
            $title = substr($title, 0, $settings['char_limit']);
        }

        $this->add_render_attribute( 'title', 'class', [ 'product_title', 'entry-title', 'elementor-heading-title' ] );

        $html = sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['header_size'], $this->get_render_attribute_string( 'title' ), $title );
        echo $html;
    }
}
