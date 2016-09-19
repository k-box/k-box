<!-- 10 -->
# Localization

Localization of the UI and messages in other languages is a key topic.

**At now only fixed text handled by the Laravel framework is available in multiple languages**.

Current language supported are:

- English
- Russian

The language localization to be showed to the user is chosen according to the [Accept-Language HTTP header (RFC2616)](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4) sent by the browser.

If a language not available is requested by the browser the system will show the English version, as a fallback.

If you feel uncomfortable with this decision [let us know](http://klink.uservoice.com/forums/303582-k-link-dms/suggestions/9463032-language-buttons-for-switching-between-russian-and).

### For the developers

The localization rules follows the [Laravel 5.0 Localization](http://laravel.com/docs/5.0/localization). For further reference please refer to the [Laravel Documentation](http://laravel.com/docs/5.0/localization).

