define(['i18n!localization/lang.js'], function(_strings) {
    
    // Super inspired by https://github.com/andywer/laravel-js-localization/blob/laravel-5/resources/js/localization.js
    
    /**
     * Replace variables used in the message by appropriate values.
     *
     * @param {String} message      Input message.
     * @param {Object} replacements Associative array: { variableName: "replacement", ... }
     * @return {String} The input message with all replacements applied.
     */
    var applyReplacements = function (message, replacements) {
        for (var replacementName in replacements) {
            var replacement = replacements[replacementName];

            var regex = new RegExp(':'+replacementName, 'g');
            message = message.replace(regex, replacement);
        }

        return message;
    };
    
    /**
     * Expose a Laravel Lang similar class
     */

    var Lang = {

        /**
         * Translate a message.
         *
         * @param {String} messageKey       The message key (message identifier).
         * @param {Object} [replacements]   Replacements object: { variableName: "replacement", ... }
         * @return {String} Translated message.
         */
        trans : function(messageKey, replacements) {

            if (typeof _strings[messageKey] == "undefined") {
                return messageKey;
            }

            var message = _strings[messageKey];

            if (replacements) {
                message = applyReplacements(message, replacements);
            }

            return message;
        },

        /**
         * Returns whether the given message is defined or not.
         *
         * @param {String} messageKey   Message key.
         * @return {Boolean} True if the given message exists.
         */
        has : function(messageKey) {
            return typeof _strings[messageKey] != "undefined";
        },

        /**
         * Choose one of multiple message versions, based on
         * pluralization rules. Only English pluralization
         * supported for now. If `count` is one then the first
         * version of the message is retuned, otherwise the
         * second version.
         *
         * @param {String} messageKey       Message key.
         * @param {Integer} count           Subject count for pluralization.
         * @param {Object} [replacements]   Associative array: { variableName: "replacement", ... }
         * @return {String} Translated message.
         */
        choice : function(messageKey, count, replacements) {
            if (typeof _strings[messageKey] == "undefined") {
                return messageKey;
            }

            var message;
            var messageSplitted = _strings[messageKey].split('|');
            
            // TODO: find a way to handle the selection of the messages, as Symfony Translation choice selection is very complicated

            if (count == 1) {
                message = messageSplitted[0];
            } else {
                message = messageSplitted[1];
            }

            if (replacements) {
                message = applyReplacements(message, replacements);
            }

            return message;
        },
        
        /**
         * Choose the translation string from messageKey or alternateMessageKey based on the existence of basedOn key in replacements
         * 
         * if baseOn key do not exists, alternateMessageKey will be used
         */
        alternate: function(messageKey, alternateMessageKey, basedOn, replacements){
            if (typeof _strings[messageKey] == "undefined" || typeof _strings[alternateMessageKey] == "undefined") {
                return messageKey;
            }
            
            var message = _strings[alternateMessageKey];
            
            if (replacements) {
                
                if(replacements[basedOn]){
                    message = _strings[messageKey]
                }
                
                message = applyReplacements(message, replacements);
            }

            return message;
        }
        
    };

    return Lang;

});