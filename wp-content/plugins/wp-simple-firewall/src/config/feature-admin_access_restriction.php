<?php
return
	sprintf(
	"---
slug: 'admin_access_restriction'
properties:
  name: '%s'
  show_feature_menu_item: true
  storage_key: 'admin_access_restriction' # should correspond exactly to that in the plugin.yaml
  tagline: '%s'
  menu_title: '%s'

admin_notices:
  'certain-options-restricted':
    id: 'certain-options-restricted'
    schedule: 'conditions'
    valid_admin: true
    type: 'warning'
  'admin-users-restricted':
    id: 'admin-users-restricted'
    schedule: 'conditions'
    valid_admin: true
    type: 'warning'

# Options Sections
sections:
  -
    slug: 'section_enable_plugin_feature_admin_access_restriction'
    primary: true
  -
    slug: 'section_admin_access_restriction_settings'
    primary: false
  -
    slug: 'section_admin_access_restriction_areas'
    primary: false
  -
    slug: 'section_non_ui'
    hidden: true

# Define Options
options:
  -
    key: 'enable_admin_access_restriction'
    section: 'section_enable_plugin_feature_admin_access_restriction'
    transferable: true
    default: 'N'
    type: 'checkbox'
    link_info: 'http://icwp.io/40'
    link_blog: 'http://icwp.io/wpsf02'
  -
    key: 'admin_access_key'
    section: 'section_enable_plugin_feature_admin_access_restriction'
    transferable: true
    sensitive: true
    default: ''
    type: 'password'
    link_info: 'http://icwp.io/42'
    link_blog: ''
  -
    key: 'admin_access_timeout'
    section: 'section_admin_access_restriction_settings'
    transferable: true
    default: 30
    type: 'integer'
    link_info: 'http://icwp.io/41'
    link_blog: ''
  -
    key: 'admin_access_restrict_options'
    section: 'section_admin_access_restriction_areas'
    transferable: true
    default: 'Y'
    type: 'checkbox'
    link_info: 'http://icwp.io/wpsf32'
    link_blog: ''
  -
    key: 'admin_access_restrict_admin_users'
    section: 'section_admin_access_restriction_areas'
    transferable: true
    default: 'N'
    type: 'checkbox'
    link_info: ''
    link_blog: ''
  -
    key: 'admin_access_restrict_plugins'
    section: 'section_admin_access_restriction_areas'
    transferable: true
    type: 'multiple_select'
    default:
    value_options:
      -
        value_key: 'activate_plugins'
        text: 'Activate'
      -
        value_key: 'install_plugins'
        text: 'Install'
      -
        value_key: 'update_plugins'
        text: 'Update'
      -
        value_key: 'delete_plugins'
        text: 'Delete'
    link_info: 'http://icwp.io/wpsf21'
    link_blog: ''
  -
    key: 'admin_access_restrict_themes'
    section: 'section_admin_access_restriction_areas'
    transferable: true
    type: 'multiple_select'
    default:
    value_options:
      -
        value_key: 'switch_themes'
        text: 'Activate'
      -
        value_key: 'edit_theme_options'
        text: 'Edit Theme Options'
      -
        value_key: 'install_themes'
        text: 'Install'
      -
        value_key: 'update_themes'
        text: 'Update'
      -
        value_key: 'delete_themes'
        text: 'Delete'
    link_info: 'http://icwp.io/wpsf21'
    link_blog: ''
  -
    key: 'admin_access_restrict_posts'
    section: 'section_admin_access_restriction_areas'
    transferable: true
    type: 'multiple_select'
    default:
    value_options:
      -
        value_key: 'edit'
        text: 'Create / Edit'
      -
        value_key: 'publish'
        text: 'Publish'
      -
        value_key: 'delete'
        text: 'Delete'
    link_info: 'http://icwp.io/wpsf21'
    link_blog: ''
  -
    key: 'current_plugin_version'
    section: 'section_non_ui'

# Definitions for constant data that doesn't need stored in the options
definitions:
  admin_access_key_cookie_name: 'icwp_wpsf_aakcook'
  admin_access_options_to_restrict:
    wpms_options:
      - 'admin_email'
      - 'site_name'
      - 'registration'
    wpms_pages:
      - 'settings.php'
    wp_options:
      - 'blogname'
      - 'blogdescription'
      - 'siteurl'
      - 'home'
      - 'admin_email'
      - 'users_can_register'
      - 'comments_notify'
      - 'comment_moderation'
      - 'blog_public'
    wp_pages:
      - 'options-general.php'
      - 'options-discussion.php'
      - 'options-reading.php'
",
		_wpsf__( 'WordPress Security Admin' ), //name
		_wpsf__( 'Protect your security plugin not just your WordPress site' ), //tagline
		_wpsf__( 'Security Admin' )
	);