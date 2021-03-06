/*  $Id$	-*- mode: javascript -*-
 *  
 *  File	font.src
 *  Part of	Cute
 *  Author	Anjo Anjewierden, a.a.anjewierden@utwente.nl
 *  Purpose	Definition of class Font
 *  Works with	SWI-Prolog (www.swi-prolog.org)
 *  
 *  Notice	Copyright (c) 2012, 2014  University of Twente
 *  
 *  History	22/07/12  (Created)
 *  		30/01/14  (Last modified)
 */

/*------------------------------------------------------------
 *  Class cute.Font
 *------------------------------------------------------------*/
/*  Use with gcc -E -x c -P -C *.h > *.js 
 */

"use strict";

(function () {
    var cute = this.cute;

    /**
     *  Create a font for drawing on the canvas from a specification or a
     *  CSS string.  
     */
    var Font = cute.Font = function(spec) {
        var spec = spec || {};
        var ft = this;

        if (arguments.length > 1) {
            throw 'cute.Font: Single specification argument or CSS string';
        }

        if (typeof(spec) === 'string') {
            ft._css = spec;
            ft._css_set = true;
        } else {
            ft._style = spec.style || "normal"; // italic, oblique
            ft._weight = spec.weight || "normal";// bold, bolder, lighter, 100, ... 900
            ft._size = spec.size || "16px";
            ft._line_height = spec.line_height || null;
            ft._family = spec.family || "sans-serif";
            ft._css = null;
            ft._css_set = false; // If true user has specified full CSS font
        }

        ft._height = 0;
        ft._ascent = 0;
        ft._descent = 0;

        ft.request_compute();

        return ft;
    }

    Font.prototype.request_compute = function() {
        this._request_compute = true;
        return this;
    }

    Font.prototype.compute = function() {
        var ft = this;

        if (ft._request_compute) {
            var text, block, div, body;

            if (!ft._css_set) {
                ft._css = ft._weight + ' ' + ft._style + ' ' + ft._size;
                if (ft._line_height !== null)
                    ft._css += '/' + ft._line_height;
                ft._css += ' ' + ft._family;
            }

            text = $('<span style="font: ' + ft._css + '">Hg</span>');
            block = $('<div style="display: inline-block; width: 1px; height: 0px;"></div>');
            div = $('<div></div>');
            body = $('body');
            div.append(text, block);
            body.append(div);

            try {
                block.css({ verticalAlign: 'baseline' });
                ft._ascent = block.offset().top - text.offset().top;
                block.css({ verticalAlign: 'bottom' });
                ft._height = Math.ceil(block.offset().top - text.offset().top);
                ft._descent = ft._height - ft._ascent;
            } finally {
                div.remove();
            }

            ft._request_compute = false;
        }

        return ft;
    }


    Font.prototype.width = function(str) {
        cute.ctx.font(this.css());

        return cute.ctx.measureText(str).width;
    }

    Font.prototype.height = function() {
        var ft = this;

        ft.compute();
        return ft._height + 15; // TBD - Hack to increase touch area
    }


    Font.prototype.ascent = function() {
        var ft = this;

        ft.compute();
        return ft._ascent;
    }


    Font.prototype.descent = function() {
        var ft = this;

        ft.compute();
        return ft._descent;
    }


    Font.prototype.family = function(fam) {
        var f = this;

        if (fam === undefined)
            return f._family;

        if (f._family != fam) {
            f._family = fam;
            f._css = undefined;
            f._css_set = false;
            f.request_compute();
        }

        return f;
    }


    Font.prototype.style = function(s) {
        var f = this;

        if (s === undefined)
            return f._style;

        if (f._style != s) {
            f._style = s;
            f._css = undefined;
            f._css_set = false;
            f.request_compute();
        }

        return f;
    }


    Font.prototype.weight = function(w) {
        var f = this;

        if (w === undefined)
            return f._weight;

        if (f._weight != w) {
            f._weight = w;
            f._css = undefined;
            f._css_set = false;
            f.request_compute();
        }

        return f;
    }


    Font.prototype.css = function(font) {
        var ft = this;

        if (font === undefined) {
            ft.compute();
            return ft._css;
        }

        ft._css = font;
        ft._css_set = true;
        ft.request_compute();
        ft.compute();

        return ft;
    }
}).call(this);
