<?php
/*
 * Plugin Name: Third Wunder Case Studies Plugin
 * Version: 1.0
 * Plugin URI: http://www.thirdwunder.com/
 * Description: Third Wunder case studies CPT plugin
 * Author: Mohamed Hamad
 * Author URI: http://www.thirdwunder.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: tw-case-studies-plugin
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Mohamed Hamad
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Load plugin class files
require_once( 'includes/class-tw-case-studies-plugin.php' );
require_once( 'includes/class-tw-case-studies-plugin-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-tw-case-studies-plugin-admin-api.php' );
require_once( 'includes/lib/class-tw-case-studies-plugin-post-type.php' );
require_once( 'includes/lib/class-tw-case-studies-plugin-taxonomy.php' );

if(!class_exists('AT_Meta_Box')){
  require_once("includes/My-Meta-Box/meta-box-class/my-meta-box-class.php");
}

/**
 * Returns the main instance of TW_Case_Studies_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object TW_Case_Studies_Plugin
 */
function TW_Case_Studies_Plugin () {
	$instance = TW_Case_Studies_Plugin::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = TW_Case_Studies_Plugin_Settings::instance( $instance );
	}

	return $instance;
}

TW_Case_Studies_Plugin();

$case_study_slug = get_option('wpt_tw_case_study_slug') ? get_option('wpt_tw_case_study_slug') : "faq";
$case_study_search = get_option('wpt_tw_case_study_search') ? true : false;
$case_study_archive = get_option('wpt_tw_case_study_archive') ? true : false;

$case_study_category = get_option('wpt_tw_case_study_category') ? get_option('wpt_tw_case_study_category') : "off";
$case_study_tag      = get_option('wpt_tw_case_study_tag') ? get_option('wpt_tw_case_study_tag') : "off";


$case_study_testimonials = get_option('wpt_tw_case_study_testimonials') ? get_option('wpt_tw_case_study_testimonials') : "off";
$case_study_client       = get_option('wpt_tw_case_study_client')       ? get_option('wpt_tw_case_study_client') : "off";
$case_study_project      = get_option('wpt_tw_case_study_project')      ? get_option('wpt_tw_case_study_project') : "off";

TW_Case_Studies_Plugin()->register_post_type(
                        'tw_case_study',
                        __( 'Case Studies',     'tw-case-studies-plugin' ),
                        __( 'Case Study',       'tw-case-studies-plugin' ),
                        __( 'Case Studies CPT', 'tw-case-studies-plugin'),
                        array(
                          'menu_icon'=>plugins_url( 'assets/img/cpt-icon-case-study.png', __FILE__ ),
                          'rewrite' => array('slug' => $case_study_slug),
                          'exclude_from_search' => $case_study_search,
                          'has_archive'     => $case_study_archive,
                        )
                    );

if($case_study_category=='on'){
  TW_Case_Studies_Plugin()->register_taxonomy( 'tw_case_study_category', __( 'Case Study Categories', 'tw-case-studies-plugin' ), __( 'Case Study Category', 'tw' ), 'tw_case_study', array('hierarchical'=>true) );
}

if($case_study_tag=='on'){
 TW_Case_Studies_Plugin()->register_taxonomy( 'tw_case_study_tag', __( 'Case Study Tags', 'tw-case-studies-plugin' ), __( 'Case Study Tag', 'tw-case-studies-plugin' ), 'tw_case_study', array('hierarchical'=>false) );
}


if (is_admin()){
  $case_study_config = array(
    'id'             => 'tw_case_study_cpt_metabox',
    'title'          => 'Case Study Details',
    'pages'          => array('tw_case_study'),
    'context'        => 'normal',
    'priority'       => 'high',
    'fields'         => array(),
    'local_images'   => true,
    'use_with_theme' => false
  );
  $case_study_meta =  new AT_Meta_Box($case_study_config);

  $case_study_meta->addFile('tw_case_study_pdf',array('name'=> 'PDF Download','desc'=>'Case Study pdf download', 'ext' =>'pdf','mime_type' => 'application/pdf'));

  if(is_plugin_active('tw-clients-plugin/tw-clients-plugin.php') && $case_study_client=='on'){
    $case_study_meta->addPosts('tw_case_study_client',array('post_type' => 'tw_client'),array('name'=> 'Client'));
  }

  if(is_plugin_active('tw-projects-plugin/tw-projects-plugin.php') && $case_study_project=='on'){
    $case_study_meta->addPosts('tw_case_study_project',array('post_type' => 'tw_project'),array('name'=> 'Project'));
  }

  if( is_plugin_active( 'tw-testimonials-plugin/tw-testimonials-plugin.php' ) && $case_study_testimonials=='on' ){
    $case_study_meta->addPosts('tw_case_study_testimonials',array('post_type' => 'tw_testimonial', 'type'=>'checkbox_list'),array('name'=> 'Testimonials'));
  }


  $case_study_meta->Finish();
}