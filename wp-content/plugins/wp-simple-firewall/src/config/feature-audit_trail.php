{
  "slug": "audit_trail",
  "properties": {
    "slug": "audit_trail",
    "name": "Audit Trail",
    "show_feature_menu_item": true,
    "storage_key": "audit_trail",
    "tagline": "Get a view on what happens on your site, when it happens",
    "show_central": true,
    "access_restricted": true,
    "premium": false,
    "has_custom_actions": true,
    "order": 110
  },
  "sections": [
    {

      "slug": "section_enable_plugin_feature_audit_trail",
      "primary": true,
      "title": "Enable Plugin Feature: Audit Trail",
      "title_short": "Enable / Disable",
      "summary": [
        "Purpose - The Audit Trail is designed so you can look back on events and analyse what happened and what may have gone wrong.",
        "Recommendation - Keep the Audit Trail feature turned on."
      ]
    },
    {
      "slug": "section_audit_trail_options",
      "title": "Audit Trail Options",
      "title_short": "Options",
      "summary": [
        "Purpose - Provides finer control over the audit trail itself.",
        "Recommendation - These settings are dependent on your requirements."
      ]
    },
    {
      "slug": "section_enable_audit_contexts",
      "title": "Enable Audit Contexts",
      "title_short": "Audit Contexts",
      "summary":
      [
        "Purpose - Specify which types of actions on your site are logged.",
        "Recommendation - These settings are dependent on your requirements."
      ]
    },
    {
      "slug": "section_non_ui",
      "hidden": true
    }
  ],
  "options": [
    {
      "key": "enable_audit_trail",
      "section": "section_enable_plugin_feature_audit_trail",
      "default": "N",
      "type": "checkbox",
      "link_info": "http://icwp.io/5p",
      "link_blog": "http://icwp.io/a1",
      "name": "Enable Audit Trail",
      "summary": "Enable (or Disable) The Audit Trail Feature",
      "description": "Checking/Un-Checking this option will completely turn on/off the whole Audit Trail feature"

    },
    {
      "key": "audit_trail_auto_clean",
      "section": "section_audit_trail_options",
      "default": 14,
      "type": "integer",
      "link_info": "http://icwp.io/a2",
      "link_blog": "http://icwp.io/a1",
      "name": "Auto Clean",
      "summary": "Enable Audit Auto Cleaning",
      "description": "Events older than the number of days specified will be automatically cleaned from the database"
    },
    {
      "key": "audit_trail_max_entries",
      "section": "section_audit_trail_options",
      "premium": true,
      "default": 1000,
      "type": "integer",
      "link_info": "",
      "link_blog": "",
      "name": "Max Trail Length",
      "summary": "Maximum Audit Trail Length To Keep",
      "description": "Automatically remove any audit trail entries when this limit is exceeded."
    },
    {
      "key": "enable_audit_context_users",
      "section": "section_enable_audit_contexts",
      "default": "Y",
      "type": "checkbox",
      "link_info": "http://icwp.io/a3",
      "link_blog": "http://icwp.io/a1",
      "name": "Users And Logins",
      "summary": "Enable Audit Context - Users And Logins",
      "description": "When this context is enabled, the audit trail will track activity relating to: Users And Logins"
    },
    {
      "key": "enable_audit_context_plugins",
      "section": "section_enable_audit_contexts",
      "default": "Y",
      "type": "checkbox",
      "link_info": "http://icwp.io/a3",
      "link_blog": "http://icwp.io/a1",
      "name": "Plugins",
      "summary": "Enable Audit Context - Plugins",
      "description": "When this context is enabled, the audit trail will track activity relating to: WordPress Plugins"
    },
    {
      "key": "enable_audit_context_themes",
      "section": "section_enable_audit_contexts",
      "default": "Y",
      "type": "checkbox",
      "link_info": "http://icwp.io/a3",
      "link_blog": "http://icwp.io/a1",
      "name": "Themes",
      "summary": "Enable Audit Context - Themes",
      "description": "When this context is enabled, the audit trail will track activity relating to: WordPress Themes"
    },
    {
      "key": "enable_audit_context_posts",
      "section": "section_enable_audit_contexts",
      "default": "Y",
      "type": "checkbox",
      "link_info": "http://icwp.io/a3",
      "link_blog": "http://icwp.io/a1",
      "name": "Posts And Pages",
      "summary": "Enable Audit Context - Posts And Pages",
      "description": "When this context is enabled, the audit trail will track activity relating to: Editing and publishing of posts and pages"
    },
    {
      "key": "enable_audit_context_wordpress",
      "section": "section_enable_audit_contexts",
      "default": "Y",
      "type": "checkbox",
      "link_info": "http://icwp.io/a3",
      "link_blog": "http://icwp.io/a1",
      "name": "WordPress And Settings",
      "summary": "Enable Audit Context - WordPress And Settings",
      "description": "When this context is enabled, the audit trail will track activity relating to: WordPress upgrades and changes to particular WordPress settings"
    },
    {
      "key": "enable_audit_context_emails",
      "section": "section_enable_audit_contexts",
      "default": "Y",
      "type": "checkbox",
      "link_info": "http://icwp.io/a3",
      "link_blog": "http://icwp.io/a1",
      "name": "Emails",
      "summary": "Enable Audit Context - Emails",
      "description": "When this context is enabled, the audit trail will track activity relating to: Email Sending"
    },
    {
      "key": "enable_audit_context_wpsf",
      "section": "section_enable_audit_contexts",
      "default": "Y",
      "type": "checkbox",
      "link_info": "http://icwp.io/a4",
      "link_blog": "http://icwp.io/a1",
      "name": "Shield",
      "summary": "Enable Audit Context - Shield",
      "description": "When this context is enabled, the audit trail will track activity relating to: Shield"
    }
  ],
  "definitions": {
    "audit_trail_default_per_page": 25,
    "audit_trail_default_max_entries": 50,
    "audit_trail_table_name": "audit_trail",
    "audit_trail_table_columns": [
      "id",
      "wp_username",
      "ip",
      "context",
      "event",
      "category",
      "message",
      "immutable",
      "created_at",
      "deleted_at"
    ]
  }
}