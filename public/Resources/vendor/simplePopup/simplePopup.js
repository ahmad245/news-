/**
* Libairie simplePopup
* Utilisé pour afficher des popup
* Exemple : simplePopup.openWindow("http://google.fr/", "1080px", "50%")
* Léo GRAND - 26/07/2017
*/
(function(window) {

    "use strict";

    /**
     * Affiche les erreurs
     * @param {string} err - l'erreur a afficher
     */
    function errorDisplay(err) {
        window.console.error("simplePopup error: " + err);
        return false;
    }

    /**
     * Parse la valeur de taille de la popup
     * @param {string} param - une valeur en pixel, % ou ratio
     * @return {Object} - un obj contenant des infos sur la taille
     */
    function parseSize(param) {

        var result = {
            size: "",
            isRatio: false,
            isPourcentage: false,
            isPixel: false
        };

        var size = param.toLowerCase().replace(" ", "");

        /** Pixel */
        if (size.match(/px/)) {

            result.isPixel = true;
            result.size = Number(size.slice(0, -2));

            // Verifie que size est bien un nombre, si non, size est NaN
            if (result.size) {
                return result;
            } else {
                return errorDisplay("expected valid number");
            }
        }

        /** Ratio */
        else if (size.match(/ratio:/)) {

            result.isRatio = true;
            result.size = Number(size.slice(6));

            // Verifie que size est bien un nombre, si non, size est NaN
            if (result.size) {
                return result;
            } else {
                return errorDisplay("expected valid number");
            }
        }

        /** % */
        else if (size.match(/%/)) {

            result.isPourcentage = true;
            result.size = Number(size.slice(0, -1));

            // Verifie que size est bien un nombre, si non, size est NaN
            if (result.size) {
                return result;
            } else {
                return errorDisplay("expected valid number");
            }
        }

        /** Par default pixel */
        else {
            result.isPixel = true;
            result.size = Number(size);

            // Verifie que size est bien un nombre, si non, size est NaN
            if (result.size) {
                return result;
            } else {
                return errorDisplay("expected valid number");
            }
        }
    }


    /**
     * Transforme les valeurs demandé en pixel
     * @param {string} width - largeur de la popup 
     * @param {string} height - hauteur de la popup
     * @return {Object} result - objet contenant la taille en pixel
     */
    function getPixel(width, height) {

        var result = {
            height: "",
            width: ""
        };

        var size = {
            height: parseSize(height),
            width: parseSize(width)
        };

        var sizeScreen = {
            height: screen.availHeight,
            width: screen.availWidth
        };

        if (size.height.isPixel) {
            result.height = size.height.size;
        }

        if (size.height.isPourcentage) {
            result.height = size.height.size * sizeScreen.height / 100;
        }

        if (size.width.isPixel) {
            result.width = size.width.size;
        }

        if (size.width.isPourcentage) {
            result.width = size.width.size * sizeScreen.width / 100;
        }

        if (size.width.isRatio) {
            result.width = result.height * size.width.size;
        }

        if (size.height.isRatio) {
            result.height = result.width * size.height.size;
        }

        return result;
    }

    /**
     * Trouve l'ecran et position la popup
     * @param {string} w - largeur de la popup
     * @param {string} h - hauteur de la popup
     */
    function findCenter(w, h) {
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;

        return {
            top: top,
            left: left
        };
    }

    /**
     * Ouvre la popup
     * @param {string} url - url de ouvrir dans la popup
     * @param {Object} windowSize - size de la popup
     * @param {bool} centered
     * @param {string} windowName - nom de la popup
     * @param {bool} onlyFocusIfOpened - si true et que la fenêtre avec cet id est ouverte, fait uniquement un focus sans changer son URL
     */
    function windowOpener(url, windowSize, centered, windowName, onlyFocusIfOpened) {
        if(typeof windowName === "undefined") {
            windowName = "";
        }
        
        var windowFeatures = 'height=' + windowSize.height + ', width=' + windowSize.width;
        
        if (centered) {
            var center = findCenter(windowSize.width, windowSize.height);
            
            windowFeatures += ', top=' + center.top + ' , left=' + center.left
        }
    
        if(onlyFocusIfOpened && windowName !== "") {
            var testWindow = window.open("", windowName, windowFeatures);
        
            if(testWindow.location.href !== 'about:blank') {
                testWindow.focus();
                return testWindow;
            }
        }
        
        return window.open(url, windowName, windowFeatures);
    }

    /**
     * Display errors
     * @param {string} url - url a verifier
     * @return {bool} - false if error, true if valid
     */
    function validateParams(url) {
        if (typeof url != "string") {
            errorDisplay("url must be a string, got" + typeof url);
            return false;
        }
        return true;
    }

    /**
     * Ouvre une page dans une autre fenetre
     * @param {string} url - url a ouvrir dans la popup
     * @param {string} width - largeur de la popup (px, % ou ratio:)
     * @param {string} height - hauteur de la popup (px, % ou ratio:)
     * @param {bool} center - si vide ou true, la popup est centré
     * @param {string} windowName - nom de la popup
     * @param {bool} onlyFocusIfOpened - si true et que la fenêtre avec cet id est ouverte, fait uniquement un focus sans changer son URL
     */
    function openWindow(url, width, height, center, windowName, onlyFocusIfOpened) {

        if (typeof center === "undefined") {
            center = true;
        }

        var val = validateParams(url);

        if (val) {
            var windowConstructor = getPixel(width, height);
            var openedWindow = windowOpener(url, windowConstructor, center, windowName, onlyFocusIfOpened);
            setTimeout(function() {
                openedWindow.resizeTo(windowConstructor.width, windowConstructor.height);
            }, 50);
        }
    }

    /**
     * Défini la lib et l'inject dans window
     * @return {Object}
     */
    function define() {
        var simplePopup = {};
        simplePopup.openWindow = openWindow;
        return simplePopup;
    }

    if (typeof(simplePopup) === 'undefined') {
        window.simplePopup = define();
    }

})(window);