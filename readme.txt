=== HTML Forms ===
Contributors: Ibericode, DvanKooten, hchouhan, lapzor
Donate link: https://www.htmlformsplugin.com/#utm_source=wp-plugin-repo&utm_medium=html-forms&utm_campaign=donate-link
Tags: form, forms, contact form, html form, htmlforms
Requires at least: 4.6
Tested up to: 6.1
Stable tag: 1.3.26
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 5.3

Not just another contact form plugin.

== Description ==

With [HTML Forms](https://www.htmlformsplugin.com/), you can easily add multi-purpose forms to your WordPress site.

The idea behind HTML Forms is different than most other form builder plugins: **You manage the form HTML. The plugin manages the PHP and a tiny bit of JavaScript**.

That's right. No "intuitive" drag & drop interface. We believe that dynamic form builders can be great, but they also severely limit your options. Also, they're slow and overly complicated from a technical point of view.

HTML Forms aims to be simpler, faster and smarter.

You define your form fields in HTML and the plugin takes care of submitting the form, saving the data and running a configurable set of form actions.

> ### 1. Define your form fields
> You can use anything that is valid HTML. Just ensure all `input` elements in your form have a `name` attribute to save the data entered in that field.
> ### 2. Configure your form actions
> By default, form submissions are automatically saved for you. You can configure several other actions to run when a form is submitted, like sending an email notification or subscribing to Mailchimp.
> ### 3. Show the form somewhere on your site
> Show your form by using the `[hf_form]` shortcode in your WordPress posts, pages or widget areas.

## HTML Forms features

- Full control over the HTML for the form fields. If you want, we'll help you in generating the field HTML though.
- Each form submission is automatically saved in your database and can be viewed in a simple table format.
- Configure an unlimited amount of actions to run when a form is successfully submitted. For example, sending out email notifications.
- Access form field values by referencing the field name, eg `[NAME]` or `[EMAIL]`.
- Hide form or redirect to URL after a successful submission.
- Configurable & translatable form messages.
- Field validation.
- Developer friendly. HTML Forms comes with a myriad of [JavaScript events](https://www.htmlformsplugin.com/kb/javascript-events/) and WordPress hooks that allow you to modify or extend the plugin's default behavior.

## Who is this for?

HTML Forms is for everyone that wants a flexible & high performing form solution.

- Users. Managing your own site? With HTML Forms, you don't need any technical knowledge to setup a contact form.
- Developers. Building a site for your client? You supply the form HTML, HTML Forms takes care of validating and processing the form.
- Theme developers. Shipping a theme? HTML Forms allows for complete blend-in with your theme, and you don't have to write yet another contact form plugin.

With HTML Forms you can create any type of form. Contact forms, registration forms, price quote forms, you name it. It's somewhat similar to Contact Form 7, but without yet another template language for you to learn.

For more information, please visit [htmlformsplugin.com](https://www.htmlformsplugin.com/).

### Support

The [HTML Forms knowledge base](https://www.htmlformsplugin.com/kb/) covers a wide range of frequently asked questions.

Use the [WordPress.org plugin forums](https://wordpress.org/support/plugin/html-forms) for community support where we try to help all of our users. If you found a bug, please create an issue on Github where we can act upon them more efficiently.

### Contributing

You can contribute to HTML Forms in various ways. For example:

- Write about the plugin on your blog or share it on social media.
- [Translate the plugin into your language](https://translate.wordpress.org/projects/wp-plugins/html-forms/stable/) using your WordPress.org account.
- Leave feedback on issues in GitHub: [ibericode/html-forms](https://github.com/ibericode/html-forms)

== Installation ==

1. In your WordPress admin panel, go to *Plugins > New Plugin*, search for **HTML Forms** and click "*Install now*"
1. Alternatively, download the plugin and upload the contents of `html-forms.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the plugin

For more information, please refer to the [HTML Forms Knowledge Base](https://www.htmlformsplugin.com/kb/#utm_source=wp-plugin-repo&utm_medium=html-forms&utm_campaign=installation-instructions).

== Frequently Asked Questions ==

#### Where can I find help?
Start by going through the [HTML Forms knowledge base](https://www.htmlformsplugin.com/kb/#utm_source=wp-plugin-repo&utm_medium=html-forms&utm_campaign=plugin-faq) where we cover a wide range of frequently asked questions.

#### How to display a form in posts or pages?
Use the `[hf_form]` shortcode.

#### How to display a form in widget areas like the sidebar or footer?
Go to **Appearance > Widgets**, add the "Text Widget" to any of your widget areas and use the `[hf_form]` shortcode.

#### How do I show a form in a pop-up?

We recommend the [Boxzilla pop-up plugin](https://wordpress.org/plugins/boxzilla/) for this. You can use the `[hf_form]` shortcode in your pop-up box to render any of your forms.

#### Can I send an email when the form is submitted?

Yes! You can configure this by opening up the "Actions" tab when editing your form and clicking the "Email" button under "Available actions".

### Does the plugin include anti-spam measures?

The plugin comes with built-in spam protection that should stop all automated attacks. When the built-in protection doesn't cut it, [WPBruiser](https://wordpress.org/plugins/goodbye-captcha/) surely will.


== Screenshots ==

1. Overview of forms in HTML Forms.
2. Editing form fields.
3. Sending an email when a form is submitted.
4. Viewing saved form submissions.
5. Hide form or redirect to URL after form submission.
6. Configurable form messages.
7. Details of a specific form submission.


== Changelog ==


#### 1.3.26 - Nov 15, 2022

- Add pagination to submissions overview page if there are more than 500 items to show.
- "Move to trash" action for submissions now properly called "Delete permanently". Thanks to [Rakesh Sabale](https://github.com/ghubrakesh).


#### 1.3.25 - Oct 21, 2022

This release fixes a SQL injection vulnerability where users with the administrator role could execute arbitrary SQL commands.
Thanks to **Nguyen Duy Quoc Khanh** via [WPScan](https://wpscan.com) for the responsible disclosure.

Other changes:

- Use default WordPress collation when creating tables. Props [bengs](https://github.com/bengs).


#### 1.3.24 - Sep 23, 2022

- Compatibility with Swup.js, thanks to [Sam Brown](https://github.com/sam98brown)
- Update JavaScript dependencies.


#### 1.3.23 - Mar 2, 2022

- Add support for swup.js in [conditional fields](https://www.htmlformsplugin.com/kb/conditional-elements/) logic.
- Safer support for Full-Site Editing by splitting up registering and enqueueing of JS and CSS assets.


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



== Upgrade Notice ==
