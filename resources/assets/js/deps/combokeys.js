!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var o;"undefined"!=typeof window?o=window:"undefined"!=typeof global?o=global:"undefined"!=typeof self&&(o=self),o.Combokeys=e()}}(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

module.exports = function (element) {
    var self = this,
        Combokeys = self.constructor;

    /**
     * a list of all the callbacks setup via Combokeys.bind()
     *
     * @type {Object}
     */
    self.callbacks = {};

    /**
     * direct map of string combinations to callbacks used for trigger()
     *
     * @type {Object}
     */
    self.directMap = {};

    /**
     * keeps track of what level each sequence is at since multiple
     * sequences can start out with the same sequence
     *
     * @type {Object}
     */
    self.sequenceLevels = {};

    /**
     * variable to store the setTimeout call
     *
     * @type {null|number}
     */
    self.resetTimer;

    /**
     * temporary state where we will ignore the next keyup
     *
     * @type {boolean|string}
     */
    self.ignoreNextKeyup = false;

    /**
     * temporary state where we will ignore the next keypress
     *
     * @type {boolean}
     */
    self.ignoreNextKeypress = false;

    /**
     * are we currently inside of a sequence?
     * type of action ("keyup" or "keydown" or "keypress") or false
     *
     * @type {boolean|string}
     */
    self.nextExpectedAction = false;

    self.element = element;

    self.addEvents();

    Combokeys.instances.push(self);
    return self;
};

module.exports.prototype.bind = require("./prototype/bind");
module.exports.prototype.bindMultiple = require("./prototype/bindMultiple");
module.exports.prototype.unbind = require("./prototype/unbind");
module.exports.prototype.trigger = require("./prototype/trigger");
module.exports.prototype.reset = require("./prototype/reset.js");
module.exports.prototype.stopCallback = require("./prototype/stopCallback");
module.exports.prototype.handleKey = require("./prototype/handleKey");
module.exports.prototype.addEvents = require("./prototype/addEvents");
module.exports.prototype.bindSingle = require("./prototype/bindSingle");
module.exports.prototype.getKeyInfo = require("./prototype/getKeyInfo");
module.exports.prototype.pickBestAction = require("./prototype/pickBestAction");
module.exports.prototype.getReverseMap = require("./prototype/getReverseMap");
module.exports.prototype.getMatches = require("./prototype/getMatches");
module.exports.prototype.resetSequences = require("./prototype/resetSequences");
module.exports.prototype.fireCallback = require("./prototype/fireCallback");
module.exports.prototype.bindSequence = require("./prototype/bindSequence");
module.exports.prototype.resetSequenceTimer = require("./prototype/resetSequenceTimer");

module.exports.instances = [];
module.exports.reset = require("./reset");

/**
 * variable to store the flipped version of MAP from above
 * needed to check if we should use keypress or not when no action
 * is specified
 *
 * @type {Object|undefined}
 */
module.exports.REVERSE_MAP = null;

},{"./prototype/addEvents":2,"./prototype/bind":3,"./prototype/bindMultiple":4,"./prototype/bindSequence":5,"./prototype/bindSingle":6,"./prototype/fireCallback":7,"./prototype/getKeyInfo":8,"./prototype/getMatches":9,"./prototype/getReverseMap":10,"./prototype/handleKey":11,"./prototype/pickBestAction":14,"./prototype/reset.js":15,"./prototype/resetSequenceTimer":16,"./prototype/resetSequences":17,"./prototype/stopCallback":18,"./prototype/trigger":19,"./prototype/unbind":20,"./reset":21}],2:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";
module.exports = function () {
    var self = this,
        addEvent,
        element = self.element,
        handleKeyEvent,
        boundHandler;

    handleKeyEvent = require("./handleKeyEvent");

    addEvent = require("../../helpers/addEvent");
    boundHandler = handleKeyEvent.bind(self);
    addEvent(element, "keypress", boundHandler);
    addEvent(element, "keydown", boundHandler);
    addEvent(element, "keyup", boundHandler);
};

},{"../../helpers/addEvent":22,"./handleKeyEvent":12}],3:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";
/**
 * binds an event to Combokeys
 *
 * can be a single key, a combination of keys separated with +,
 * an array of keys, or a sequence of keys separated by spaces
 *
 * be sure to list the modifier keys first to make sure that the
 * correct key ends up getting bound (the last key in the pattern)
 *
 * @param {string|Array} keys
 * @param {Function} callback
 * @param {string=} action - "keypress", "keydown", or "keyup"
 * @returns void
 */
module.exports = function(keys, callback, action) {
    var self = this;

    keys = keys instanceof Array ? keys : [keys];
    self.bindMultiple(keys, callback, action);
    return self;
};

},{}],4:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * binds multiple combinations to the same callback
 *
 * @param {Array} combinations
 * @param {Function} callback
 * @param {string|undefined} action
 * @returns void
 */
module.exports = function (combinations, callback, action) {
    var self = this;

    for (var j = 0; j < combinations.length; ++j) {
        self.bindSingle(combinations[j], callback, action);
    }
};

},{}],5:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * binds a key sequence to an event
 *
 * @param {string} combo - combo specified in bind call
 * @param {Array} keys
 * @param {Function} callback
 * @param {string=} action
 * @returns void
 */
module.exports = function (combo, keys, callback, action) {
    var self = this;

    // start off by adding a sequence level record for this combination
    // and setting the level to 0
    self.sequenceLevels[combo] = 0;

    /**
     * callback to increase the sequence level for this sequence and reset
     * all other sequences that were active
     *
     * @param {string} nextAction
     * @returns {Function}
     */
    function increaseSequence(nextAction) {
        return function() {
            self.nextExpectedAction = nextAction;
            ++self.sequenceLevels[combo];
            self.resetSequenceTimer();
        };
    }

    /**
     * wraps the specified callback inside of another function in order
     * to reset all sequence counters as soon as this sequence is done
     *
     * @param {Event} e
     * @returns void
     */
    function callbackAndReset(e) {
        var characterFromEvent;
        self.fireCallback(callback, e, combo);

        // we should ignore the next key up if the action is key down
        // or keypress.  this is so if you finish a sequence and
        // release the key the final key will not trigger a keyup
        if (action !== "keyup") {
            characterFromEvent = require("../../helpers/characterFromEvent");
            self.ignoreNextKeyup = characterFromEvent(e);
        }

        // weird race condition if a sequence ends with the key
        // another sequence begins with
        setTimeout(
            function() {
                self.resetSequences();
            },
            10
        );
    }

    // loop through keys one at a time and bind the appropriate callback
    // function.  for any key leading up to the final one it should
    // increase the sequence. after the final, it should reset all sequences
    //
    // if an action is specified in the original bind call then that will
    // be used throughout.  otherwise we will pass the action that the
    // next key in the sequence should match.  this allows a sequence
    // to mix and match keypress and keydown events depending on which
    // ones are better suited to the key provided
    for (var j = 0; j < keys.length; ++j) {
        var isFinal = j + 1 === keys.length;
        var wrappedCallback = isFinal ? callbackAndReset : increaseSequence(action || self.getKeyInfo(keys[j + 1]).action);
        self.bindSingle(keys[j], wrappedCallback, action, combo, j);
    }
};

},{"../../helpers/characterFromEvent":23}],6:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * binds a single keyboard combination
 *
 * @param {string} combination
 * @param {Function} callback
 * @param {string=} action
 * @param {string=} sequenceName - name of sequence if part of sequence
 * @param {number=} level - what part of the sequence the command is
 * @returns void
 */
module.exports = function (combination, callback, action, sequenceName, level) {
    var self = this;

    // store a direct mapped reference for use with Combokeys.trigger
    self.directMap[combination + ":" + action] = callback;

    // make sure multiple spaces in a row become a single space
    combination = combination.replace(/\s+/g, " ");

    var sequence = combination.split(" "),
        info;

    // if this pattern is a sequence of keys then run through this method
    // to reprocess each pattern one key at a time
    if (sequence.length > 1) {
        self.bindSequence(combination, sequence, callback, action);
        return;
    }

    info = self.getKeyInfo(combination, action);

    // make sure to initialize array if this is the first time
    // a callback is added for this key
    self.callbacks[info.key] = self.callbacks[info.key] || [];

    // remove an existing match if there is one
    self.getMatches(info.key, info.modifiers, {type: info.action}, sequenceName, combination, level);

    // add this call back to the array
    // if it is a sequence put it at the beginning
    // if not put it at the end
    //
    // this is important because the way these are processed expects
    // the sequence ones to come first
    self.callbacks[info.key][sequenceName ? "unshift" : "push"]({
        callback: callback,
        modifiers: info.modifiers,
        action: info.action,
        seq: sequenceName,
        level: level,
        combo: combination
    });
};

},{}],7:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * actually calls the callback function
 *
 * if your callback function returns false this will use the jquery
 * convention - prevent default and stop propogation on the event
 *
 * @param {Function} callback
 * @param {Event} e
 * @returns void
 */
module.exports = function (callback, e, combo, sequence) {
    var self = this,
        preventDefault,
        stopPropagation;

    // if this event should not happen stop here
    if (self.stopCallback(e, e.target || e.srcElement, combo, sequence)) {
        return;
    }

    if (callback(e, combo) === false) {
        preventDefault = require("../../helpers/preventDefault");
        preventDefault(e);
        stopPropagation = require("../../helpers/stopPropagation");
        stopPropagation(e);
    }
};

},{"../../helpers/preventDefault":27,"../../helpers/stopPropagation":32}],8:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * Gets info for a specific key combination
 *
 * @param  {string} combination key combination ("command+s" or "a" or "*")
 * @param  {string=} action
 * @returns {Object}
 */
module.exports = function (combination, action) {
    var self = this,
        keysFromString,
        keys,
        key,
        j,
        modifiers = [],
        SPECIAL_ALIASES,
        SHIFT_MAP,
        isModifier;

    keysFromString = require("../../helpers/keysFromString");
    // take the keys from this pattern and figure out what the actual
    // pattern is all about
    keys = keysFromString(combination);

    SPECIAL_ALIASES = require("../../helpers/special-aliases");
    SHIFT_MAP = require("../../helpers/shift-map");
    isModifier = require("../../helpers/isModifier");
    for (j = 0; j < keys.length; ++j) {
        key = keys[j];

        // normalize key names
        if (SPECIAL_ALIASES[key]) {
            key = SPECIAL_ALIASES[key];
        }

        // if this is not a keypress event then we should
        // be smart about using shift keys
        // this will only work for US keyboards however
        if (action && action !== "keypress" && SHIFT_MAP[key]) {
            key = SHIFT_MAP[key];
            modifiers.push("shift");
        }

        // if this key is a modifier then add it to the list of modifiers
        if (isModifier(key)) {
            modifiers.push(key);
        }
    }

    // depending on what the key combination is
    // we will try to pick the best event for it
    action = self.pickBestAction(key, modifiers, action);

    return {
        key: key,
        modifiers: modifiers,
        action: action
    };
};

},{"../../helpers/isModifier":25,"../../helpers/keysFromString":26,"../../helpers/shift-map":28,"../../helpers/special-aliases":29}],9:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * finds all callbacks that match based on the keycode, modifiers,
 * and action
 *
 * @param {string} character
 * @param {Array} modifiers
 * @param {Event|Object} e
 * @param {string=} sequenceName - name of the sequence we are looking for
 * @param {string=} combination
 * @param {number=} level
 * @returns {Array}
 */
module.exports = function (character, modifiers, e, sequenceName, combination, level) {
    var self = this,
        j,
        callback,
        matches = [],
        action = e.type,
        isModifier,
        modifiersMatch;

    // if there are no events related to this keycode
    if (!self.callbacks[character]) {
        return [];
    }

    isModifier = require("../../helpers/isModifier");
    // if a modifier key is coming up on its own we should allow it
    if (action === "keyup" && isModifier(character)) {
        modifiers = [character];
    }

    // loop through all callbacks for the key that was pressed
    // and see if any of them match
    for (j = 0; j < self.callbacks[character].length; ++j) {
        callback = self.callbacks[character][j];

        // if a sequence name is not specified, but this is a sequence at
        // the wrong level then move onto the next match
        if (!sequenceName && callback.seq && self.sequenceLevels[callback.seq] !== callback.level) {
            continue;
        }

        // if the action we are looking for doesn't match the action we got
        // then we should keep going
        if (action !== callback.action) {
            continue;
        }

        // if this is a keypress event and the meta key and control key
        // are not pressed that means that we need to only look at the
        // character, otherwise check the modifiers as well
        //
        // chrome will not fire a keypress if meta or control is down
        // safari will fire a keypress if meta or meta+shift is down
        // firefox will fire a keypress if meta or control is down
        modifiersMatch = require("./modifiersMatch");
        if ((action === "keypress" && !e.metaKey && !e.ctrlKey) || modifiersMatch(modifiers, callback.modifiers)) {

            // when you bind a combination or sequence a second time it
            // should overwrite the first one.  if a sequenceName or
            // combination is specified in this call it does just that
            //
            // @todo make deleting its own method?
            var deleteCombo = !sequenceName && callback.combo === combination;
            var deleteSequence = sequenceName && callback.seq === sequenceName && callback.level === level;
            if (deleteCombo || deleteSequence) {
                self.callbacks[character].splice(j, 1);
            }

            matches.push(callback);
        }
    }

    return matches;
};

},{"../../helpers/isModifier":25,"./modifiersMatch":13}],10:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * reverses the map lookup so that we can look for specific keys
 * to see what can and can't use keypress
 *
 * @return {Object}
 */
module.exports = function () {
    var self = this,
        constructor = self.constructor,
        SPECIAL_KEYS_MAP;

    if (!constructor.REVERSE_MAP) {
        constructor.REVERSE_MAP = {};
        SPECIAL_KEYS_MAP = require("../../helpers/special-keys-map");
        for (var key in SPECIAL_KEYS_MAP) {

            // pull out the numeric keypad from here cause keypress should
            // be able to detect the keys from the character
            if (key > 95 && key < 112) {
                continue;
            }

            if (SPECIAL_KEYS_MAP.hasOwnProperty(key)) {
                constructor.REVERSE_MAP[SPECIAL_KEYS_MAP[key]] = key;
            }
        }
    }
    return constructor.REVERSE_MAP;
};

},{"../../helpers/special-keys-map":31}],11:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * handles a character key event
 *
 * @param {string} character
 * @param {Array} modifiers
 * @param {Event} e
 * @returns void
 */
module.exports = function (character, modifiers, e) {
    var self = this,
        callbacks,
        j,
        doNotReset = {},
        maxLevel = 0,
        processedSequenceCallback = false,
        isModifier,
        ignoreThisKeypress;

    callbacks = self.getMatches(character, modifiers, e);
    // Calculate the maxLevel for sequences so we can only execute the longest callback sequence
    for (j = 0; j < callbacks.length; ++j) {
        if (callbacks[j].seq) {
            maxLevel = Math.max(maxLevel, callbacks[j].level);
        }
    }

    // loop through matching callbacks for this key event
    for (j = 0; j < callbacks.length; ++j) {

        // fire for all sequence callbacks
        // this is because if for example you have multiple sequences
        // bound such as "g i" and "g t" they both need to fire the
        // callback for matching g cause otherwise you can only ever
        // match the first one
        if (callbacks[j].seq) {

            // only fire callbacks for the maxLevel to prevent
            // subsequences from also firing
            //
            // for example 'a option b' should not cause 'option b' to fire
            // even though 'option b' is part of the other sequence
            //
            // any sequences that do not match here will be discarded
            // below by the resetSequences call
            if (callbacks[j].level !== maxLevel) {
                continue;
            }

            processedSequenceCallback = true;

            // keep a list of which sequences were matches for later
            doNotReset[callbacks[j].seq] = 1;
            self.fireCallback(callbacks[j].callback, e, callbacks[j].combo, callbacks[j].seq);
            continue;
        }

        // if there were no sequence matches but we are still here
        // that means this is a regular match so we should fire that
        if (!processedSequenceCallback) {
            self.fireCallback(callbacks[j].callback, e, callbacks[j].combo);
        }
    }

    // if the key you pressed matches the type of sequence without
    // being a modifier (ie "keyup" or "keypress") then we should
    // reset all sequences that were not matched by this event
    //
    // this is so, for example, if you have the sequence "h a t" and you
    // type "h e a r t" it does not match.  in this case the "e" will
    // cause the sequence to reset
    //
    // modifier keys are ignored because you can have a sequence
    // that contains modifiers such as "enter ctrl+space" and in most
    // cases the modifier key will be pressed before the next key
    //
    // also if you have a sequence such as "ctrl+b a" then pressing the
    // "b" key will trigger a "keypress" and a "keydown"
    //
    // the "keydown" is expected when there is a modifier, but the
    // "keypress" ends up matching the nextExpectedAction since it occurs
    // after and that causes the sequence to reset
    //
    // we ignore keypresses in a sequence that directly follow a keydown
    // for the same character
    ignoreThisKeypress = e.type === "keypress" && self.ignoreNextKeypress;
    isModifier = require("../../helpers/isModifier");
    if (e.type === self.nextExpectedAction && !isModifier(character) && !ignoreThisKeypress) {
        self.resetSequences(doNotReset);
    }

    self.ignoreNextKeypress = processedSequenceCallback && e.type === "keydown";
};

},{"../../helpers/isModifier":25}],12:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * handles a keydown event
 *
 * @param {Event} e
 * @returns void
 */
module.exports = function (e) {
    var self = this,
        characterFromEvent,
        eventModifiers;

    // normalize e.which for key events
    // @see http://stackoverflow.com/questions/4285627/javascript-keycode-vs-charcode-utter-confusion
    if (typeof e.which !== "number") {
        e.which = e.keyCode;
    }
    characterFromEvent = require("../../helpers/characterFromEvent");
    var character = characterFromEvent(e);

    // no character found then stop
    if (!character) {
        return;
    }

    // need to use === for the character check because the character can be 0
    if (e.type === "keyup" && self.ignoreNextKeyup === character) {
        self.ignoreNextKeyup = false;
        return;
    }

    eventModifiers = require("../../helpers/eventModifiers");
    self.handleKey(character, eventModifiers(e), e);
};

},{"../../helpers/characterFromEvent":23,"../../helpers/eventModifiers":24}],13:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * checks if two arrays are equal
 *
 * @param {Array} modifiers1
 * @param {Array} modifiers2
 * @returns {boolean}
 */
module.exports = function (modifiers1, modifiers2) {
    return modifiers1.sort().join(",") === modifiers2.sort().join(",");
};

},{}],14:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * picks the best action based on the key combination
 *
 * @param {string} key - character for key
 * @param {Array} modifiers
 * @param {string=} action passed in
 */
module.exports = function (key, modifiers, action) {
    var self = this;

    // if no action was picked in we should try to pick the one
    // that we think would work best for this key
    if (!action) {
        action = self.getReverseMap()[key] ? "keydown" : "keypress";
    }

    // modifier keys don't work as expected with keypress,
    // switch to keydown
    if (action === "keypress" && modifiers.length) {
        action = "keydown";
    }

    return action;
};

},{}],15:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * resets the library back to its initial state. This is useful
 * if you want to clear out the current keyboard shortcuts and bind
 * new ones - for example if you switch to another page
 *
 * @returns void
 */
module.exports = function() {
    var self = this;
    self.callbacks = {};
    self.directMap = {};
    return this;
};

},{}],16:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";
/**
 * called to set a 1 second timeout on the specified sequence
 *
 * this is so after each key press in the sequence you have 1 second
 * to press the next key before you have to start over
 *
 * @returns void
 */
module.exports = function () {
    var self = this;

    clearTimeout(self.resetTimer);
    self.resetTimer = setTimeout(
        function() {
        self.resetSequences();
        },
        1000
    );
};

},{}],17:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * resets all sequence counters except for the ones passed in
 *
 * @param {Object} doNotReset
 * @returns void
 */
module.exports = function (doNotReset) {
    var self = this;

    doNotReset = doNotReset || {};

    var activeSequences = false,
        key;

    for (key in self.sequenceLevels) {
        if (doNotReset[key]) {
            activeSequences = true;
            continue;
        }
        self.sequenceLevels[key] = 0;
    }

    if (!activeSequences) {
        self.nextExpectedAction = false;
    }
};

},{}],18:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
* should we stop this event before firing off callbacks
*
* @param {Event} e
* @param {Element} element
* @return {boolean}
*/
module.exports = function(e, element) {

    // if the element has the class "combokeys" then no need to stop
    if ((" " + element.className + " ").indexOf(" combokeys ") > -1) {
        return false;
    }

    // stop for input, select, and textarea
    return element.tagName === "INPUT" || element.tagName === "SELECT" || element.tagName === "TEXTAREA" || element.isContentEditable;
};

},{}],19:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";
/**
 * triggers an event that has already been bound
 *
 * @param {string} keys
 * @param {string=} action
 * @returns void
 */
module.exports = function(keys, action) {
    if (directMap[keys + ":" + action]) {
        directMap[keys + ":" + action]({}, keys);
    }
    return this;
};

},{}],20:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";
/**
 * unbinds an event to Combokeys
 *
 * the unbinding sets the callback function of the specified key combo
 * to an empty function and deletes the corresponding key in the
 * directMap dict.
 *
 * TODO: actually remove this from the callbacks dictionary instead
 * of binding an empty function
 *
 * the keycombo+action has to be exactly the same as
 * it was defined in the bind method
 *
 * @param {string|Array} keys
 * @param {string} action
 * @returns void
 */
module.exports = function(keys, action) {
    var self = this;

    return self.bind(keys, function() {}, action);
};

},{}],21:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

module.exports = function () {
    var self = this;

    self.instances.forEach(function(combokeys) {
        combokeys.reset();
    });
};

},{}],22:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * cross browser add event method
 *
 * @param {Element|HTMLDocument} object
 * @param {string} type
 * @param {Function} callback
 * @returns void
 */
module.exports = function (object, type, callback) {
    if (object.addEventListener) {
        object.addEventListener(type, callback, false);
        return;
    }

    object.attachEvent("on" + type, callback);
};

},{}],23:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * takes the event and returns the key character
 *
 * @param {Event} e
 * @return {string}
 */
module.exports = function (e) {
    var SPECIAL_KEYS_MAP,
        SPECIAL_CHARACTERS_MAP;
    SPECIAL_KEYS_MAP = require("./special-keys-map");
    SPECIAL_CHARACTERS_MAP = require("./special-characters-map");


    // for keypress events we should return the character as is
    if (e.type === "keypress") {
        var character = String.fromCharCode(e.which);

        // if the shift key is not pressed then it is safe to assume
        // that we want the character to be lowercase.  this means if
        // you accidentally have caps lock on then your key bindings
        // will continue to work
        //
        // the only side effect that might not be desired is if you
        // bind something like 'A' cause you want to trigger an
        // event when capital A is pressed caps lock will no longer
        // trigger the event.  shift+a will though.
        if (!e.shiftKey) {
            character = character.toLowerCase();
        }

        return character;
    }

    // for non keypress events the special maps are needed
    if (SPECIAL_KEYS_MAP[e.which]) {
        return SPECIAL_KEYS_MAP[e.which];
    }

    if (SPECIAL_CHARACTERS_MAP[e.which]) {
        return SPECIAL_CHARACTERS_MAP[e.which];
    }

    // if it is not in the special map

    // with keydown and keyup events the character seems to always
    // come in as an uppercase character whether you are pressing shift
    // or not.  we should make sure it is always lowercase for comparisons
    return String.fromCharCode(e.which).toLowerCase();
};

},{"./special-characters-map":30,"./special-keys-map":31}],24:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * takes a key event and figures out what the modifiers are
 *
 * @param {Event} e
 * @returns {Array}
 */
module.exports = function (e) {
    var modifiers = [];

    if (e.shiftKey) {
        modifiers.push("shift");
    }

    if (e.altKey) {
        modifiers.push("alt");
    }

    if (e.ctrlKey) {
        modifiers.push("ctrl");
    }

    if (e.metaKey) {
        modifiers.push("meta");
    }

    return modifiers;
};

},{}],25:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * determines if the keycode specified is a modifier key or not
 *
 * @param {string} key
 * @returns {boolean}
 */
module.exports = function (key) {
    return key === "shift" || key === "ctrl" || key === "alt" || key === "meta";
};

},{}],26:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * Converts from a string key combination to an array
 *
 * @param  {string} combination like "command+shift+l"
 * @return {Array}
 */
module.exports = function (combination) {
    if (combination === "+") {
        return ["+"];
    }

    return combination.split("+");
};

},{}],27:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * prevents default for this event
 *
 * @param {Event} e
 * @returns void
 */
module.exports = function (e) {
    if (e.preventDefault) {
        e.preventDefault();
        return;
    }

    e.returnValue = false;
};

},{}],28:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";
/**
 * this is a mapping of keys that require shift on a US keypad
 * back to the non shift equivelents
 *
 * this is so you can use keyup events with these keys
 *
 * note that this will only work reliably on US keyboards
 *
 * @type {Object}
 */
module.exports = {
    "~": "`",
    "!": "1",
    "@": "2",
    "#": "3",
    "$": "4",
    "%": "5",
    "^": "6",
    "&": "7",
    "*": "8",
    "(": "9",
    ")": "0",
    "_": "-",
    "+": "=",
    ":": ";",
    "\"": "'",
    "<": ",",
    ">": ".",
    "?": "/",
    "|": "\\"
};

},{}],29:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";
/**
 * this is a list of special strings you can use to map
 * to modifier keys when you specify your keyboard shortcuts
 *
 * @type {Object}
 */
module.exports = {
    "option": "alt",
    "command": "meta",
    "return": "enter",
    "escape": "esc",
    "mod": /Mac|iPod|iPhone|iPad/.test(navigator.platform) ? "meta" : "ctrl"
};

},{}],30:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";
/**
 * mapping for special characters so they can support
 *
 * this dictionary is only used incase you want to bind a
 * keyup or keydown event to one of these keys
 *
 * @type {Object}
 */
module.exports = {
    106: "*",
    107: "+",
    109: "-",
    110: ".",
    111: "/",
    186: ";",
    187: "=",
    188: ",",
    189: "-",
    190: ".",
    191: "/",
    192: "`",
    219: "[",
    220: "\\",
    221: "]",
    222: "'"
};

},{}],31:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";
/**
 * mapping of special keycodes to their corresponding keys
 *
 * everything in this dictionary cannot use keypress events
 * so it has to be here to map to the correct keycodes for
 * keyup/keydown events
 *
 * @type {Object}
 */
module.exports = {
    8: "backspace",
    9: "tab",
    13: "enter",
    16: "shift",
    17: "ctrl",
    18: "alt",
    20: "capslock",
    27: "esc",
    32: "space",
    33: "pageup",
    34: "pagedown",
    35: "end",
    36: "home",
    37: "left",
    38: "up",
    39: "right",
    40: "down",
    45: "ins",
    46: "del",
    91: "meta",
    93: "meta",
    187: "plus",
    189: "minus",
    224: "meta"
};

/**
 * loop through the f keys, f1 to f19 and add them to the map
 * programatically
 */
for (var i = 1; i < 20; ++i) {
    module.exports[111 + i] = "f" + i;
}

/**
 * loop through to map numbers on the numeric keypad
 */
for (i = 0; i <= 9; ++i) {
    module.exports[i + 96] = i;
}

},{}],32:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

/**
 * stops propogation for this event
 *
 * @param {Event} e
 * @returns void
 */
module.exports = function (e) {
    if (e.stopPropagation) {
        e.stopPropagation();
        return;
    }

    e.cancelBubble = true;
};

},{}],33:[function(require,module,exports){
/* eslint-env node, browser */
"use strict";

module.exports = require("./Combokeys");

},{"./Combokeys":1}]},{},[33])(33)
});