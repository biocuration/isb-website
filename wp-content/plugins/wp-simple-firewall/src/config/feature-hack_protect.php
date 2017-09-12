{
  "slug": "hack_protect",
  "properties": {
    "slug": "hack_protect",
    "name": "Hack Protection",
    "show_feature_menu_item": true,
    "storage_key": "hack_protect",
    "show_central": true,
    "access_restricted": true,
    "order": 70
  },
  "sections": [
    {
      "slug": "section_enable_plugin_feature_hack_protection_tools",
      "primary": true,
      "title": "Enable Plugin Feature: Hack Protection",
      "title_short": "Enable / Disable",
      "summary": [
        "Purpose - The Hack Protection system is a set of tools to warn you and protect you against hacks on your site.",
        "Recommendation - Keep the Hack Protection feature turned on."
      ]
    },
    {
      "slug": "section_core_file_integrity_scan",
      "title": "Core File Integrity Scanner",
      "title_short": "Core File Scanner",
      "summary": [
        "Purpose - Regularly scan your WordPress core files for changes compared to official WordPress files.",
        "Recommendation - Keep the Core File Integrity Scanner feature turned on."
      ]
    },
    {
      "slug": "section_unrecognised_file_scan",
      "title": "Unrecognised Files Scanner",
      "title_short": "Unrecognised Files Scanner",
      "summary": [
        "Purpose - Scan your WordPress core folders for unrecognised files that don't belong.",
        "Recommendation - Keep the Unrecognised Files Scanner feature turned on."
      ]
    },
    {
      "slug": "section_non_ui",
      "hidden": true
    }
  ],
  "options": [
    {
      "key": "enable_hack_protect",
      "section": "section_enable_plugin_feature_hack_protection_tools",
      "default": "Y",
      "type": "checkbox",
      "link_info": "http://icwp.io/wpsf38",
      "link_blog": "http://icwp.io/9x",
      "name": "Enable Hack Protection",
      "summary": "Enable (or Disable) The Hack Protection Feature",
      "description": "Checking/Un-Checking this option will completely turn on/off the whole Hack Protection feature"
    },
    {
      "key": "enable_core_file_integrity_scan",
      "section": "section_core_file_integrity_scan",
      "default": "Y",
      "type": "checkbox",
      "link_info": "http://icwp.io/wpsf36",
      "link_blog": "http://icwp.io/wpsf37",
      "name": "Core File Scanner",
      "summary": "Daily Cron - Scans WordPress Core Files For Alterations",
      "description": "Compares all WordPress core files on your site against the official WordPress files. WordPress Core files should never be altered for any reason."
    },
    {
      "key": "attempt_auto_file_repair",
      "section": "section_core_file_integrity_scan",
      "default": "N",
      "type": "checkbox",
      "link_info": "http://icwp.io/wpsf36",
      "link_blog": "http://icwp.io/wpsf37",
      "name": "Auto Repair",
      "summary": "Automatically Repair WordPress Core Files That Have Been Altered",
      "description": "Attempts to automatically repair WordPress Core files with the official WordPress file data, for files that have been altered or are missing."
    },
    {
      "key": "enable_unrecognised_file_cleaner_scan",
      "section": "section_unrecognised_file_scan",
      "default": "enabled_report_only",
      "type": "select",
      "value_options": [
        {
          "value_key": "disabled",
          "text": "Scan Disabled"
        },
        {
          "value_key": "enabled_report_only",
          "text": "Email Report Only"
        },
        {
          "value_key": "enabled_delete_only",
          "text": "Automatically Delete Files"
        },
        {
          "value_key": "enabled_delete_report",
          "text": "Auto Delete Files and Email Report"
        }
      ],
      "link_info": "http://icwp.io/9y",
      "link_blog": "http://icwp.io/95",
      "name": "Unrecognised Files Scanner",
      "summary": "Scans Core Directories For Unrecognised Files",
      "description": "Scans for, and automatically deletes, any files in your core WordPress folders that are not part of your WordPress installation."
    },
    {
      "key": "ufc_scan_uploads",
      "section": "section_unrecognised_file_scan",
      "default": "N",
      "type": "checkbox",
      "link_info": "http://icwp.io/95",
      "link_blog": "",
      "name": "Scan Uploads",
      "summary": "Scan Uploads Folder For PHP and Javascript",
      "description": "The Uploads folder is primarily for media, but could be used to store nefarious files."
    },
    {
      "key": "ufc_exclusions",
      "section": "section_unrecognised_file_scan",
      "default": [
      	"error_log",
      	".htaccess",
      	".htpasswd",
      	".user.ini",
      	"php.ini",
      	"web.config",
      	"php_mail.log",
      	"mail.log"
      ],
      "type": "array",
      "link_info": "http://icwp.io/9z",
      "link_blog": "http://icwp.io/95",
      "name": "File Exclusions",
      "summary": "Provide A List Of Files To Be Excluded From The Scan",
      "description": "Take a new line for each file you wish to exclude from the scan. No commas are necessary."
    }
  ],
  "definitions": {
    "plugin_vulnerabilities_data_source": "https://raw.githubusercontent.com/FernleafSystems/wp-plugin-vulnerabilities/master/vulnerabilities.yaml",
    "notifications_cron_name": "plugin-vulnerabilities-notification",
    "corechecksum_cron_name": "core-checksum-notification",
    "unrecognisedscan_cron_name": "unrecognised-scan-notification",
    "url_checksum_api": "https://api.wordpress.org/core/checksums/1.0/",
    "url_wordress_core_svn": "https://core.svn.wordpress.org/",
    "url_wordress_core_svn_il8n": "https://svn.automattic.com/wordpress-i18n/",
    "corechecksum_exclusions": [
      "readme.html",
      "license.txt",
      "licens-sv_SE.txt",
      "wp-config-sample.php",
      "wp-content/"
    ],
    "corechecksum_exclusions_missing_only": [
      "wp-admin/install.php",
      "xmlrpc.php"
    ],
    "corechecksum_autofix": [
      "wp-content/index.php",
      "wp-content/plugins/index.php",
      "wp-content/themes/index.php"
    ]
  }
}