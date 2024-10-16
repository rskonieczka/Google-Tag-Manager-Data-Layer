# Wordpress Google Tag Manager Data Layer

Integrates Google Tag Manager and sends various post/page and user data.

## What this plugin does

The "Google Tag Manager Data Layer" plugin allows for an advanced integration with Google Tag Manager by collecting and sending various data from WordPress. This allows for a more detailed tracking and analysis of user and page data, which is useful for advanced analytics and marketing configurations.

## Functional description

1. **Admin Panel Settings:**
    - The plugin adds a settings page in the WordPress admin panel where you can configure various options.
    - You can enter your Google Tag Manager (GTM) ID.
    - You can select which data should be sent to Google Tag Manager.
    - You can select the placement of the GTM code (header or footer of the page).
2. **Options Registration:**
    - The plugin registers options such as GTM ID, data points to send and code placement.
3. **GTM Code Placement:**
    - Based on your options, the plugin generates the Google Tag Manager code and places it in the selected location (header or footer of the page).
4. **Inclusion of UAParser.js Library:**
    - The plugin loads the UAParser.js library, which is used to collect data about the user's browser, operating system and device.
5. **Setting the Visitor Type Cookie:**
    - The plugin sets a cookie called `gtm_visitor_type` to differentiate between new and returning visitors.
6. **Data Collection and Sending to GTM:**
    - The plugin collects various data from WordPress and adds it to the Google Tag Manager data layer.
    - Data includes: page/post title, publication date, categories, tags, author, post/page ID, post type, post format, number of posts, custom terms, login status, user role, user ID, user email (with SHA256 hash), user creation date, search data on the page, site name and ID, IP address (if the user granted permission), browser data, operating system, device, visitor type (new/returning), number of comments, page template, parent ID, parent title, menu items, WordPress version, active plugins, and theme data.
