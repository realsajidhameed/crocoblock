<?php
/**
 * Plugin Name: JetEngine — Universal Shortcode Dynamic Tag (Safe)
 * Description: Adds a dynamic tag that safely executes shortcodes across all Elementor fields (text, URL, image, etc.).
 * Version:     1.0
 * Author:      Sajid
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'jet-engine/elementor-views/dynamic-tags/register', function( $dynamic_tags, $module ) {

    if ( ! class_exists( 'Universal_Shortcode_Dynamic_Tag' ) ) {

        class Universal_Shortcode_Dynamic_Tag extends \Elementor\Core\DynamicTags\Tag {

            public function get_name() {
                return 'universal-shortcode-safe';
            }

            public function get_title() {
                return __( 'Shortcode (Universal)', 'jetengine-universal-shortcode' );
            }

            public function get_group() {
                return 'jet_engine';
            }

            public function get_categories() {
                return [
                    \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
                    \Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
                    \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
                    \Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY,
                    \Elementor\Modules\DynamicTags\Module::MEDIA_CATEGORY,
                ];
            }

            protected function register_controls() {
                $this->add_control(
                    'shortcode_text',
                    [
                        'label'       => __( 'Shortcode', 'jetengine-universal-shortcode' ),
                        'type'        => \Elementor\Controls_Manager::TEXTAREA,
                        'placeholder' => '[your_shortcode]',
                        'rows'        => 4,
                        'description' => __( 'Enter the full shortcode including brackets []. Example: [your_shortcode]', 'jetengine-universal-shortcode' ),
                    ]
                );
            }

            public function render() {
                $shortcode = (string) $this->get_settings( 'shortcode_text' );
                if ( trim( $shortcode ) === '' ) {
                    return;
                }

                // Validate that it's inside brackets
                if ( strpos( $shortcode, '[' ) !== 0 || strpos( $shortcode, ']' ) === false ) {
                    echo '<span style="color:red;">Error: Invalid shortcode format. Must start with [ and end with ].</span>';
                    return;
                }

                try {
                    $output = do_shortcode( $shortcode );
                    if ( is_string( $output ) ) {
                        echo $output;
                    } else {
                        echo '<span style="color:red;">Error: Shortcode did not return a string output.</span>';
                    }
                }
                catch ( \Throwable $e ) {
                    echo '<span style="color:red;">Shortcode Error: ' . esc_html( $e->getMessage() ) . '</span>';
                }
            }
        }

    }

    if ( method_exists( $dynamic_tags, 'register' ) ) {
        $dynamic_tags->register( new Universal_Shortcode_Dynamic_Tag() );
    } else {
        $dynamic_tags->register_tag( new Universal_Shortcode_Dynamic_Tag() );
    }

}, 10, 2 );
