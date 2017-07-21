{
  "slug": "ips",
  "properties": {
    "slug": "ips",
    "name": "IP Manager",
    "show_feature_menu_item": true,
    "storage_key": "ips",
    "tagline": "Manage Visitor IP Address",
    "show_central": true,
    "order": 100
  },
  "admin_notices": {
    "visitor-whitelisted": {
      "id": "visitor-whitelisted",
      "schedule": "conditions",
      "valid_admin": true,
      "type": "info"
    }
  },
  "requirements": {
    "php": {
      "functions": [
        "filter_var"
      ],
      "constants": [
        "FILTER_VALIDATE_IP",
        "FILTER_FLAG_IPV4",
        "FILTER_FLAG_IPV6",
        "FILTER_FLAG_NO_PRIV_RANGE",
        "FILTER_FLAG_NO_RES_RANGE"
      ]
    }
  },
  "sections": [
    {
      "slug": "section_enable_plugin_feature_ips",
      "primary": true,
      "title": "Enable Plugin Feature: IP Manager",
      "title_short": "Enable / Disable",
      "summary": [
        "Purpose - The IP Manager allows you to whitelist, blacklist and configure auto-blacklist rules.",
        "Recommendation - Keep the IP Manager feature turned on. You should also carefully review the automatic black list settings."
      ]
    },
    {
      "slug": "section_auto_black_list",
      "title": "Automatic IP Black List",
      "title_short": "Auto Black List",
      "summary": [
        "Purpose - The Automatic IP Black List system will block the IP addresses of naughty visitors after a specified number of transgressions.",
        "Recommendation - Keep the Automatic IP Black List feature turned on."
      ]
    },
    {
      "slug": "section_non_ui",
      "hidden": true
    }
  ],
  "options": [
    {
      "key": "enable_ips",
      "section": "section_enable_plugin_feature_ips",
      "default": "N",
      "type": "checkbox",
      "link_info": "http://icwp.io/wpsf26",
      "link_blog": "",
      "name": "Enable IP Manager",
      "summary": "Enable (or Disable) The IP Manager Feature",
      "description": "Checking/Un-Checking this option will completely turn on/off the whole IP Manager feature"
    },
    {
      "key": "transgression_limit",
      "section": "section_auto_black_list",
      "default": 10,
      "type": "integer",
      "link_info": "http://icwp.io/wpsf24",
      "link_blog": "http://icwp.io/wpsf26",
      "name": "Transgression Limit",
      "summary": "Visitor IP address will be Black Listed after X bad actions on your site",
      "description": "A black mark is set against an IP address each time a visitor trips the defenses of the Shield plugin. When the number of these transgressions exceeds specified limit, they are automatically blocked from accessing the site. Set this to 0 to turn off the Automatic IP Black List feature."
    },
    {
      "key": "auto_expire",
      "section": "section_auto_black_list",
      "default": "minute",
      "type": "select",
      "value_options": [
        {
          "value_key": "minute",
          "text": "Minute"
        },
        {
          "value_key": "hour",
          "text": "Hour"
        },
        {
          "value_key": "day",
          "text": "Day"
        },
        {
          "value_key": "week",
          "text": "Week"
        }
      ],
      "link_info": "http://icwp.io/wpsf25",
      "link_blog": "http://icwp.io/wpsf26",
      "name": "Auto Block Expiration",
      "summary": "After 1 'X' a black listed IP will be removed from the black list",
      "description": "Permanent and lengthy IP Black Lists are harmful to performance. You should allow IP addresses on the black list to be eventually removed over time. Shorter IP black lists are more efficient and a more intelligent use of an IP-based blocking system."
    },
    {
      "key": "this_server_ip",
      "transferable": false,
      "sensitive": true,
      "section": "section_non_ui",
      "value": ""
    },
    {
      "key": "this_server_ip_last_check_at",
      "transferable": false,
      "section": "section_non_ui",
      "value": 0
    }
  ],
  "definitions": {
    "ip_lists_table_name": "ip_lists",
    "ip_list_table_columns": [
      "id",
      "ip",
      "label",
      "list",
      "ip6",
      "is_range",
      "transgressions",
      "last_access_at",
      "created_at",
      "deleted_at"
    ]
  }
}