# oc3d-ai-genius
Wordpress plugin. You can edit, generate texts, write programming codes using ChatGPT. It allows to use instructions, stored previously in database. 
== Installation ==

### Quick setup:
-Go to <YOUR_WEBSITE_URL>/wp-admin/admin.php?page=oc3daig_settings. In the ChatGPT tab fill your open AI key in the 'Open AI Key:'
Now you can use OC3D AI Genius plugin as metabox that is displayed at post, page or any other post type edit page.

### Detailed setup and configuration:

### General configuration.

1. Go to <YOUR_WEBSITE_URL>/wp-admin/admin.php?page=oc3daig_settings. In the ChatGPT tab fill your open AI key in the 'Open AI Key:'
2. You can fill other fields on this tab or leave them by default.
3. By default plugin displays AI metabox on post and page edit pages. If you want to display this metabox in the edit pages under other post types then select them on the 'Post Types' section.



### Post types setup.
The OC3D AI Genius plugin is accessible by default in the metabox displayed beneath the edit area for two post and page types. However, if desired, you have the option to enable it on other post types' edit pages. To do so, navigate to the General setup tab and select the desired post types.

== How to use plugin ==

Open any type of post edit page, such as a post or page, that is selected in the OC3D AI Genius configuration page. Then scroll down to the OC3D AI Genius metabox. There, you can enter text into the 'Text to be changed' input field. After that, you can manually input your instructions. Alternatively, you can select any previously stored instructions in the database. Additionally, you can select other parameters such as the model, temperature, and maximum length of the request and response text. Finally, click the Send button. If everything goes well, you will receive a response in the Result textarea.


== Users access ==

Users with Contributor roles and above have full access to OC3D AI Genius metaboxes. Users with an Editor role and above have access to the plugin's configuration.

