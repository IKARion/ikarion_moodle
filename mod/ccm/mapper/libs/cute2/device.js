/*  $Id$	-*- mode: javascript -*-
 *  
 *  File	device.src
 *  Part of	Cute
 *  Author	Anjo Anjewierden, a.a.anjewierden@utwente.nl
 *  Purpose	Definition of class Device
 *  Works with	SWI-Prolog (www.swi-prolog.org)
 *  
 *  Notice	Copyright (c) 2012, 2013, 2014  University of Twente
 *  
 *  History	04/07/12  (Created)
 *  		17/02/14  (Last modified)
 */

/*------------------------------------------------------------
 *  Class Device
 *------------------------------------------------------------*/
/*  Use with gcc -E -x c -P -C *.h > *.js 
 */

"use strict";

(function() {
    var cute = this.cute;

    var Device = cute.Device = function() {
        cute.Figure.call(this);
        return this;
    }

    ist.extend(Device, cute.Figure);
}).call(this);
