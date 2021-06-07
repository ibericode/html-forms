Changelog
=========

#### 1.3.22 - Jun 7, 2021

- Always load minified asset files, regardless of SCRIPT_DEBUG constant.
- Optimize JavaScript event callbacks in form editor.
- Fix required fields logic for when field names have multiple spaces in them.
- Add action parameter to AJAX endpoint for submitting forms.


#### 1.3.21 - May 14, 2021

- Add multisite compatibility for storing form submissions. Thanks to [Ismail Hardoum](https://github.com/elhardoum).
- Fix required fields logic for fields with spaces in their name.
- Fix replacement variables logic for fields with spaces or special characters in their name.
- Add nonce verification to all URL's using `_hf_admin_action`.


#### 1.3.20 - May 7, 2021

- Change type of referer_url column in database to `TEXT`.
- Pass form object to `hf_form_markup` filter hook.
- Refresh form editor after navigating via tabs.


#### 1.3.19 - Feb 16, 2021

- Add filter for file-uploads to use direct links to file instead of WP media attachment.
- Fix field variables not showing correctly in available tags for email action message body.


#### 1.3.18 - Jan 13, 2021

- Updated JS dependencies.
- Change plugin domain to htmlformsplugin.com.


#### 1.3.17 - Dec 21, 2020

- Forms are now sortable by name on overview page.
- Update internal dependencies to their latest versions.
- Add `role="alert"` to form messages.
- Optimize size of public JavaScript file (25% size reduction) by rewriting some dependencies.
- Add filter for tabs in admin area.


#### 1.3.16 - May 6, 2020

- Add filter hook for successful form responses: `hf_form_response`
- Only try to detect WPBruiser hidden input fields whenever that plugin is activated.
- Stop explicitly enabling shortcodes in text widgets as this is now handled by WordPress core (as of version 4.9).
- Explicitly set engine and charset on submissions database table.


#### 1.3.14 - Nov 6, 2019

**Improvements**

- Stop using `supress_filters` argument when retrieving forms, for a possible performance improvement.
- Prevent extra SQL query for options when global settings have not been saved yet.


#### 1.3.13 - Oct 18, 2019

**Fixes**

- Special HTML characters being encoded even in plain text emails.

**Improvements**

- HTML tags are no longer stripped from field values, so forms can now accept HTML. HTML is still escaped upon display to prevent XSS.


#### 1.3.12 - Oct 11, 2019

Compatibility with Mailchimp for WordPress 4.6.


#### 1.3.11 - Sep 17, 2019

**Improvements**

- Write results from Mailchimp action to MC4WP debug log.


#### 1.3.10 - Sep 5, 2019

**Fixes**

- PHP warning introduced in latest update when using array fields.


#### 1.3.9 - Sep 2, 2019

**Fixes**

- Strip out [WPBruiser](https://wordpress.org/plugins/goodbye-captcha/) token field to prevent it from being stored.

**Improvements**

- Add filter hooks to variables inside the email action. Thanks to [Ryan Salerno](https://github.com/ryansalerno).


#### 1.3.8 - May 28, 2019

**Fixes**

- Invalid HTML for generated textarea tag.

**Improvements**

- Improved HTML escaping, preserve double and single quotes.
- Explicitly set charset of HTML emails to UTF-8 by default.
- Escape HTML after limiting string length, not before.


#### 1.3.7 - April 8, 2019

**Additions**

- Add Gutenberg block for adding shortcode to content.
- Add setting in field builder for the `multiple` attribute on `<select>` elements.
- Add `hf_get_forms()` function for retrieving multiple form objects.

**Improvements**

- Add `edit_form` capability to user that activates the plugin.


#### 1.3.6 - March 27, 2019

**Improvements**

- Don't strip HTML from variable replacements in HTML emails. This change allows you to link to uploaded files properly.


#### 1.3.5 - January 29, 2019

**Improvements**

- Allow adding form field variables to the form's redirect URL.


#### 1.3.4 - January 9, 2019

**Improvements**

- Reload available field variables when adding action on form settings page.
- Add role="alert" attribute to all form messages.
- Emit "message" event whenever message is shown to user.


#### 1.3.3 - November 6, 2018

**Fixes**

- Fix selected state of email content type.

**Improvements**

- Add filter & action hook for extending forms with custom messages & message settings.
- Don't add line-breaks automatically when using HTML emails.
- Delete all related postmeta when deleting a submission.


#### 1.3.2 - Aug 6, 2018

**Improvements**

- Default to an empty string value in conditional fields logic. This allows you to show or hide elements when a field is empty or has not been set yet.


#### 1.3.1 - June 12, 2018

**Improvements**

- Format dates, files and arrays when using data variables.
- Format dates, files and arrays on the submissions overview and submissions details pages.
- Allow cancelling a form's submit event in JavaScript (with `event.stopPropagation()`) to prevent form submission.


#### 1.3.0 - May 28, 2018

**Fixes**

- PHP notice because of namespace import outside of any namespace.
- Removed usage of PHP 5.4+ feature.
- Enforce HTTPS in form preview iframe when WP Admin uses HTTPS but public site does not.

**Improvements**

- Hook into GDPR Personal Data Export & Erase functionality. Requires WP 4.9.6.
- Allow updating stored Submissions with empty values. [Allows clearing out IP address & user agents](https://github.com/ibericode/html-forms-code-snippets/blob/master/do-not-store-ip-address-and-user-agent.php).
- Internal field should start with underscore to hide it in column view.
- Add for attribute to generated label element. Allows for simple [multi-step forms](https://github.com/ibericode/html-forms-code-snippets/blob/master/multi-step-form.html).

**Additions**

- Add support for button clicks in conditional element logic.


#### 1.2.0 - April 24, 2018

**Fixes**

- Remove use of short-array syntax, which is only available in PHP 5.4 or later.

**Improvements**

- You can now hide columns on the submissions tab using screen options (in the top right corner).
- You can now disable saving submissions on a per-form basis.
- Allow pre-checking multiple checkboxes when using the field helper
- Fields with matching URL parameter names will automatically be prefilled.
- Use SVG icon in admin menu.

**Additions**

- Add simple action for subscribing to Mailchimp. Requires the [Mailchimp for WordPress plugin](https://wordpress.org/plugins/mailchimp-for-wp/).


#### 1.1.5 - April 9, 2018

**Fixes**

- Conditionally hidden fields are now ignored in server-side required field validation too. Thanks [Jeroen Sormani](https://github.com/JeroenSormani)!

**Improvements**

- Added foundational stuff for being able to handle file uploads.

**Additions**

- Added `hf_process_form` action to execute code before the form actions run.


#### 1.1.4 - March 28, 2018

**Fixes**

- Ensure form is fully functional in live preview.
- Show all forms on the forms overview page (limit was 5).
- Parse field variables in custom email headers. Thanks [Jeroen Sormani](https://github.com/JeroenSormani)!

**Improvements**

- You can now [bind JavaScript events](https://www.htmlformsplugin.com/kb/javascript-events/) using the default browser API, eg `document.getElementById('hf-form-5').addEventListener('hf-success', ..);`
- Only load JavaScript file on pages with a form on it. 
- Don't add line-breaks in HTML generated by the Field Builder.
- [Conditional elements](https://www.htmlformsplugin.com/kb/conditional-elements/) now accept wildcard values: `*`
- Available field variables in email action settings are now clickable.

**Additions**

- Added several action & filter hooks to enable advanced functionality in [HTML Forms Premium](https://www.htmlformsplugin.com/premium-features/).


#### 1.1.3 - February 12, 2018

**Fixes**

- Form preview was not working when WPML was activated.

**Improvements**

- Improved theme compatibility for the form preview.
- Print submission details as JSON instead of PHP object.
- Form messages are now added to the element as data attributes, so they can be used in JavaScript event callbacks.

**Additions**

- [Conditional logic](https://www.htmlformsplugin.com/kb/conditional-elements/) now accepts multiple values, separated by the |-character.


#### 1.1.2 - January 18, 2018

**Fixes**

- Conditional elements visible in form preview.
- WP_List_Table issue on forms overview page.

**Improvements**

- Allow disabling submission storage through a global setting.
- Hide submissions tab when submission storage is disabled.
- Prevent PHP notice if `$_SERVER` global is missing properties.
- Update JavaScript dependencies.

**Additions**

- Filter: `hf_ignored_field_names`


#### 1.1.1 - December 21, 2017

**Fixes**

- Incorrect role capability for administrators.


#### 1.1 - December 21, 2017

**Fixes**

- JavaScript error in some older browser when submitting the form.

**Improvements**

- Use custom user capability base for editing & viewing forms.
- Delay form response until a later hook so other plugins get a chance to hook in.
- Disable client-side validation for conditional fields marked as required.

**Additions**

- Added live preview to the form editor.


#### 1.0.6 - December 11, 2017

**Fixes**

- Array replacements in email message were showing "Array" instead of a comma separated list of values.
- Don't reset form when there are errors. Thanks [Jeroen Sormani](https://github.com/JeroenSormani)!

**Additions**

- You can now use certain [template variables](https://www.htmlformsplugin.com/kb/template-variables/) in the form content which will be dynamically replaced. 
- Added `hf_validate_form_{$form_slug}` filter hook.
- Added `hf_form_{$form_slug}_success` action hook.


#### 1.0.5 - November 18, 2017

**Fixes**

- Could not save more than one "Email" action.


#### 1.0.4 - November 10, 2017

**Fixes**

- Incompatibility with PHP versions before 5.6.
- Data variables could not be placed on the same line.

**Improvements**

- Clear output buffer before sending AJAX response to prevent issues with response parsing.

**Additions**

- Added `hf_form_message_{$code}` filter hook.



#### 1.0.3 - November 6, 2017

**Additions**

- Added [support for conditional elements](https://www.htmlformsplugin.com/kb/conditional-elements/) by using `data-show-if` and `data-hide-if` attributes.

**Improvements**

- Accept `id` argument in `[hf_form]` shortcode.
- Catch errors in shortcode's `slug` attribute.
- Allow changing form slug after initial form is saved.

**Fixes**

- Fixes stylesheet URL when option to load stylesheet is toggled.



#### 1.0.2 - October 30, 2017

**Fixes**

- Form validation always failing when form has 0 required fields.

**Improvements**

- Fake success response when honeypot validation fails.
- Validate request by comparing size of POST array with number of form fields.
- Ensure submit button never has label element when using the field helper.
- Optimize URL generation of asset files on frontend.

**Additions**

- Added `hf_validate_form_request_size` filter hook.


#### 1.0.1 - October 28, 2017

**Improvements**
- Added SVG admin menu icon.
- Field names are now sanitized before they are saved in the database.
- Submit button was missing for default form fields.
- Unneeded `<form>` tags are now stripped from the form before saving.

**Additions**
- Added `data-title` and `data-slug` attributes to the `<form>` element on the frontend.


#### 1.0 - October 25, 2017

Introducing a first version of HTML Forms, a different approach to forms for WordPress sites.


