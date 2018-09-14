/*  $Id$	-*- mode: javascript -*-
 *  
 *  File	expression.src
 *  Part of	CAP
 *  Author	Anjo Anjewierden, a.a.anjewierden@utwente.nl
 *  Purpose	Expression definition
 *  Works with	JavaScript
 *  
 *  Notice	Copyright (c) 2013  University of Twente
 *  
 *  History	31/07/13  (Created)
 *  		26/12/13  (Last modified)
 */

/*------------------------------------------------------------
 *  Directives
 *------------------------------------------------------------*/
/*  Use with gcc -E -x c -P -C *.h > *.js 
 */

"use strict";

/*  Conventions:

JavaScript objects and types
============================

For JavaScript objects (created with a constructor), the properties are
prefixed with an underscore.  For example, the property "name" is:

  this._name

Outside the object's class these properties are accessed with this.name().

For JavaScript object literals (without a constructor), the properties are
*not* prefixed with an underscore and the properties can be accessed directly.


JavaScript data types
=====================

- null: Used to indicate that a value is not available.  For example, if a
  Variable has not been assigned a value it is null.

- undefined: Used for missing arguments to functions.  This is most frequently
  used for functions that can either get or set a value.  For example, 

  Thing.name('hello');        -- Sets the name.
  Thing.name();               -- Returns the name


Terminology
===========

.evaluate() - used to evaluate an expression and return a value (or null).  In
general, evaluate() should be used rather than .value().
*/

(function() {
    var cap = this.cap = {};

    var current_id = 0;

    cap.var_option = function(value) {
        if (value instanceof Variable)
            return value;
        return null;
    }

    function id() {
        return 'id' + current_id++;
    }

    function unit_html(unit) { // TBD
        var html;

        switch (unit) {
        case 'g/cm^3': html = 'g/cm<sup>3</sup';
            break;
        case 'cm^3': html = 'cm<sup>3</sup';
            break;
        default:
            html = unit;
        }

        return html;
    }

    var System;

    var Thing; // With properties

    var BaseUnit;
    var DerivedUnit;
    var PrefixUnit;

    var Variable; // When in an expression it is called Handle
    var Property; // Of an object

    var Base;
    var Literal;
    var Number;
    var Handle; // Necessary because a single variable / property 
       // may be in several places in an expression 
    var Quantity; // Of a property
    var Expression;
    var Equation;
    var Add;
    var Subtract;
    var Multiply;
    var Divide;
    var Negate;
    var Identity;
    var Power;
    var Sine;
    var Cosine;
    var ArcSine;
    var ArcCosine;
    var ArcTangent;
    var Tangent;
    var Sqrt;

    var Any; // Logical variable
    var AnyNumber;
    var Contains;
    var Uncontains;

    cap.unit = {}; // Collection of units
    cap.prefix = {};

    function simplify_unit(str) {
        switch(str.replace(/\s/g,'')) { // TBD - remove regex
        case 'cm^2': return str;
        case 'cm^3': return str;
        case 'cm^3^0.33': return 'cm';
        case 'cm^3^0.333': return 'cm';
        case 'cm^3^0.3333': return 'cm';
        case 'cm^3^0.33333': return 'cm';
        case 'cm^3^0.333333': return 'cm';
        case 'cm^3^0.3333333': return 'cm';
        case 'cm^3^0.33333333': return 'cm';
        case 'cm^3^0.333333333': return 'cm';
        case 'cm^3^0.3333333333': return 'cm';
        case 'cm^3^0.33333333333': return 'cm';
        case 'cm^3^0.333333333333': return 'cm';
        case 'cm^3^0.3333333333333': return 'cm';
        case 'cm^3^0.33333333333333': return 'cm';
        case 'cm^3^0.333333333333333': return 'cm';
        case 'cm^3^0.3333333333333333': return 'cm';
        case 'cm^3^0.33333333333333333': return 'cm';
        case 'cm^3^0.333333333333333333': return 'cm';
        case 'cm^3^0.3333333333333333333': return 'cm';
        case 'cm^3^0.33333333333333333333': return 'cm';
        case 'g/cm^3': return str;
        case 'g/g/cm^3': return 'cm^3';
        case 'cm^3*g/cm^3': return 'g';
        case 'cm^3/cm*cm': return 'cm';
        case 'cm^3/cm^2': return 'cm';
        case 'cm^2*cm': return 'cm^3';
        case 'cm*cm': return 'cm^2';
        case 'cm*cm^2': return 'cm^3';
        case 'g/cm^3*cm^3': return 'g';
        case 'g/cm^3/g/cm^3': return null;
        case 'cm^3/cm^3': return null;
        default:
            throw 'cap.implify_unit ' + str + ' not simplified';
     return str;
        }
    }

    function translate(str) {
        if (cap.translate_function) {
            return cap.translate_function.call(null, str);
        }
        return str;
    }

    var Unit = cap.Unit = function(atts) {
        var unit = this;

        unit._id = id();
        unit._name = atts.name;
        unit._symbol = atts.symbol;
        unit._label = atts.label || unit._name;

        return unit;
    }

    Unit.prototype.id = function() {
        return this._id;
    }

    /*------------------------------------------------------------
     *  Quantities
     *------------------------------------------------------------*/

    Quantity = cap.Quantity = function(atts) {
        var quan = this;

        quan._value = atts.value === undefined ? null : atts.value;
        quan._unit = atts.unit === undefined ? null : atts.unit;
        quan._direction = atts.direction === undefined ? null : atts.direction;

        return this;
    }

    Quantity.prototype.toString = function() {
        return this._value + ' ' + this._unit;
    }

    Quantity.prototype.copy = function() {
        return new Quantity({
            value: this._value,
            unit: this._unit,
            direction: this._direction
        });
    }

    Quantity.prototype.unknown = function() {
        return this._value === null;
    }

    Quantity.prototype.value = function() {
        return this._value;
    }

    Quantity.prototype.unit = function() {
        return this._unit;
    }

    Quantity.prototype.direction = function() {
        return this._direction;
    }

    Quantity.prototype.coerce = function(q) {
        if (q instanceof Quantity)
            return q;
        if (typeof(q) === 'number')
            return new Quantity({value: q});
        return null;
    }


    /*------------------------------------------------------------
     *  Base units
     *------------------------------------------------------------*/

    BaseUnit = cap.Unit = function(atts) {
        var unit = this;

        unit._name = atts.name;
        unit._label = atts.label;
        unit._symbol = atts.symbol;
        unit._definition = atts.definition || '';

        return unit;
    }

    ist.extend(BaseUnit, Unit);

    DerivedUnit = cap.DerivedUnit = function(atts) {
        var unit = this;

        BaseUnit.call(unit, atts);

        unit._expression = atts.expression;

        return unit;
    }

    ist.extend(DerivedUnit, BaseUnit);

    /*------------------------------------------------------------
     *  Prefixes for units
     *------------------------------------------------------------*/

    PrefixUnit = cap.PrefixUnit = function(atts) {
        var pref = this;

        pref._id = id();
        pref._name = atts.name;
        pref._label = atts.label;
        pref._symbol = atts.symbol;
        pref._factor = atts.factor;

        return pref;
    }

    PrefixUnit.prototype.id = function() {
        return this._id;
    }

 // Define the base prefixes
    function define_base_prefixes() {
        cap.prefix.deca = new PrefixUnit(
            { name: 'deca',
              symbol: 'de',
              factor: 10
            });
        cap.prefix.hecto = new PrefixUnit(
            { name: 'hecto',
              symbol: 'h',
              factor: 100
            });
        cap.prefix.kilo = new PrefixUnit(
            { name: 'kilo',
              symbol: 'k',
              factor: 1000
            });
        cap.prefix.mega = new PrefixUnit(
            { name: 'mega',
              symbol: 'M',
              factor: 10E6
            });
        cap.prefix.giga = new PrefixUnit(
            { name: 'giga',
              symbol: 'G',
              factor: 10E9
            });
        cap.prefix.tera = new PrefixUnit(
            { name: 'tera',
              symbol: 'G',
              factor: 10E12
            });
        cap.prefix.peta = new PrefixUnit(
            { name: 'peta',
              symbol: 'P',
              factor: 10E15
            });
        cap.prefix.exa = new PrefixUnit(
            { name: 'peta',
              symbol: 'E',
              factor: 10E18
            });
        cap.prefix.zetta = new PrefixUnit(
            { name: 'zetta',
              symbol: 'E',
              factor: 10E21
            });
        cap.prefix.yotta = new PrefixUnit(
            { name: 'yotta',
              symbol: 'E',
              factor: 10E24
            });
        cap.prefix.deci = new PrefixUnit(
            { name: 'deci',
              symbol: 'd',
              factor: 10E-1
            });
        cap.prefix.centi = new PrefixUnit(
            { name: 'centi',
              symbol: 'c',
              factor: 10E-2
            });
        cap.prefix.milli = new PrefixUnit(
            { name: 'milli',
              symbol: 'm',
              factor: 10E-3
            });
        cap.prefix.micro = new PrefixUnit(
            { name: 'micro',
              symbol: '\u00B5',
              factor: 10E-6
            });
        cap.prefix.nano = new PrefixUnit(
            { name: 'nano',
              symbol: 'n',
              factor: 10E-9
            });
        cap.prefix.pico = new PrefixUnit(
            { name: 'pico',
              symbol: 'p',
              factor: 10E-12
            });
        cap.prefix.femto = new PrefixUnit(
            { name: 'femto',
              symbol: 'f',
              factor: 10E-15
            });
        cap.prefix.atto = new PrefixUnit(
            { name: 'atto',
              symbol: 'a',
              factor: 10E-18
            });
        cap.prefix.zepto = new PrefixUnit(
            { name: 'zepto',
              symbol: 'z',
              factor: 10E-21
            });
        cap.prefix.yocto = new PrefixUnit(
            { name: 'yocto',
              symbol: 'y',
              factor: 10E-24
            });
    }

    /*------------------------------------------------------------
     *  Things (in the real world)
     *------------------------------------------------------------*/

    Thing = cap.Thing = function(atts0) {
        var atts = atts0 || {};
        var thing = this;

        thing._id = id();
        thing._name = atts.name;
        thing._properties = {}; // Indexed by name
        thing._equations = [];
        thing._system = new System(); // No name

        for (var p in atts) {
            if (atts[p] instanceof Property) {
                thing[p] = atts[p];
                thing.add_property(atts[p]);
            } else {
                var prop = thing.property(p);

                if (prop)
                    prop.value(atts[p]);
            }
        }

        return this;
    }

    Thing.prototype.set_values = function(opts) {
        var thing = this;

        for (var p in opts) {
            if (opts[p] instanceof Property) {
                thing[p] = opts[p];
                thing.add_property(opts[p]);
            } else {
                var prop = thing.property(p);

                if (prop)
                    prop.value(opts[p]);
            }
        }

        return thing;
    }

    Thing.prototype.toString = function() {
        return 'Thing(' + this._name + ')';
    }

    Thing.prototype.id = function() {
        return this._id;
    }

    Thing.prototype.pp = function() {
        var thing = this;
        var p;

        ist.printf('Thing ' + this._name);
        ist.printf('  Properties');
        for (p in thing._properties) {
            ist.printf('    ' + p + ' ' + thing._properties[p]);
        }
        ist.printf('  Equations');
        for (r=0; r<thing._equations.length; r++) {
            ist.printf('    ' + thing._equations[r]);
        }

        return thing;
    }

    Thing.prototype.defined = function() {
        var thing = this;

        for (var p in thing) {
            if (thing[p] instanceof Property)
                thing.add_property(thing[p]);
        }
        return thing;
    }

    Thing.prototype.system = function() {
        return this._system;
    }

    Thing.prototype.add_property = function(prop, name0) {
        var thing = this;
        var name = name0 || prop.name();

        if (!(prop instanceof Property))
            throw 'Thing.add_property not a property ' + prop;

        prop.thing(thing);
        thing._properties[name] = prop;

        return thing;
    }

    Thing.prototype.delete_property = function(prop) {
        var thing = this;

        if (prop instanceof Property)
            return thing.delete_property(prop.name());

        delete thing._properties[prop.name()];

        return thing;
    }

    Thing.prototype.add_equation = function(eq) {
        var thing = this;

        if (!eq instanceof Equation)
            throw 'cap.Thing.add_equation(): not an equation ' + eq;

        eq.thing(thing);
        this._equations.push(eq);

        if (thing._system)
            thing._system.add_equation(eq);

        return thing;
    }

    Thing.prototype.delete_equation = function(rel) {
        var thing = this;

        for (var i=0; i<thing._equations.length; i++) {
            if (thing._equations[i] === rel) {
                thing._equations.splice(i,1);
                return thing;
            }
        }

        return thing;
    }

    Thing.prototype.equations = function() {
        return this._equations;
    }

    Thing.prototype.property = function(name) {
        var thing = this;

        if (thing._properties[name] instanceof Property)
            return thing[name];
        for (var p in thing) {
            if (thing[p] instanceof Property && thing[p]._name === name)
                return thing[p];
        }

        return undefined;
    }

    Thing.prototype.property_value = function(prop, val) {
        var thing = this;
        var p = thing.property(prop);

        if (p === undefined) {
            return prop + ' undefined property';
        }

        if (val === undefined) {
            return p.value();
        }

        p.value(val);
    }

    Thing.prototype.set = function(prop, val_unit) {
        var thing = this;
        var p = thing.property(prop);

        if (p === undefined)
            return undefined;
        p._value = val_unit.value;
        p._unit = val_unit.unit || p._unit;

        return thing;
    }

    Thing.prototype.properties = function() {
        var obj = this;
        var props = [];

        for (var p in this) {
            if (obj[p] instanceof Property)
                props.push(obj[p]);
        }
        return props;
    }

    Thing.prototype.solve = function() {
        return this._system.solve();
    }


    /*------------------------------------------------------------
     *  Base: abstract class for everything (except variables)
     *------------------------------------------------------------*/

    Base = cap.Base = function() {
        this._id = id();
        this._parent = null;
        this._value = null;
        this._thing = null;

        return this;
    }

    Base.prototype.evaluate = function() {
        throw 'cap.Base.evaluate not defined for ' + this;
    }

    Base.prototype.id = function() {
        return this._id;
    }

    Base.prototype.thing = function(thing) {
        if (thing === undefined)
            return this._thing;
        return this._thing = thing;
    }

    Base.prototype.parent = function() {
        return this._parent;
    }

    Base.prototype.unify = function() {
        return false;
    }

    Base.prototype.deunify = function() {
        return this;
    }

    Base.prototype.equal = function() {
        return false;
    }

    Base.prototype.root = function() {
        if (this._parent)
            return this._parent.root();
        return this;
    }

    Base.prototype.depth = function() {
        if (this._parent)
            return this._parent.depth() + 1;
        return 0;
    }


    /**
     *  Rewrite an expression if it unifies and return rewritten expression.
     */
    Base.prototype.rewrite = function(from, to, state, dbg) {
        if (dbg) {
            ist.printf('rewrite');
            ist.printf('  ' + from);
            ist.printf('  ' + to);
        }

        if (state)
            state.modified = false;

        if (this.unify(from)) {
            if (dbg) {
                ist.printf('    unifies');
            }
            if (to === 'evaluate') {
                var expr = from.copy(true);
                var cp = number(expr.evaluate());
            } else
                var cp = to.copy(true);

            from.deunify();
            if (state) {
                state.count++;
                state.modified = true;
            }

            if (dbg)
                ist.printf('    ' + cp);
            return cp;
        }

        from.deunify();

        return this;
    }

    Base.prototype.contains = function(expr) {
        if (this.unify(expr))
            return true;
        return false;
    }


    /*------------------------------------------------------------
     *  Expression: abstract class for all expressions (functor, arity, args)
     *------------------------------------------------------------*/

    Expression = cap.Expression = function() {
        var expr = this;
        var len = arguments.length;
        var i;

        if (len === 0)
            throw 'cap.Expression: at least one argument required (name)';

        Base.call(expr);

        expr._functor = check_functor(arguments[0]);
        expr._args = [];

        if (len === 2 && typeof(arguments[1]) === 'array') {
            len = expr._arity = arguments[1].length;
            expr._args = arguments[1];
        } else {
            expr._arity = len - 1;
            for (i=1; i<len; i++) {
                expr._args.push(check_expression(arguments[i]));
            }
        }

        for (i=0; i<expr._arity; i++) {
            expr._args[i]._parent = expr;
        }

        return expr;

        function check_functor(func) {
            if (typeof(func) !== 'string')
                throw 'cap.Expression: ' + func + ' is not a functor';
            return func;
        }

        function check_expression(exp) {
            if (is_expression(exp))
                return exp;
            if (is_variable(exp))
                return new Handle(exp);
            if (is_string(exp))
                return new Handle(new Variable(exp));
            if (is_number(exp))
                return new Number(exp);
            if (exp instanceof Literal)
                return exp;
            if (exp instanceof Any)
                return exp;

            throw 'cap.Expression: ' + exp + ' is not a valid argument in ' + expr._functor;
        }
    }

    ist.extend(Expression, Base);

    Expression.prototype.toString = function() {
        if (this._arity === 2)
            return '(' + this._args[0] + ' ' + this._functor + ' ' + this._args[1] + ')';

        return this._functor + '(' + this._args.join(', ') + ')';
    }

    Expression.prototype.latex = function() {
        var expr = this;

        if (expr._arity === 2)
            return expr._args[0].latex() + ' ' + expr._functor + ' ' + expr._args[1].latex();

        var str = expr._functor;

        str += '(';
        for (var i=0; i<expr._arity; i++) {
            var arg = expr._args[i];

            if (arg.latex)
                str += expr._args[i].latex();
            else
                throw 'cap.Expression.latex: no latex for ' + arg;
            if (i+1 < expr._arity)
                str += ', ';
        }
        str += ')';

        return str;
    }

    Expression.prototype.copy = function(unify) {
        var args = [];

        for (var i=0; i<this._arity; i++) {
            args.push(this._args[i].copy(unify));
        }

        return new Expression(this._functor, args);
    }

    Expression.prototype.equal = function(expr2) {
        var expr1 = this;

        if (expr1._functor === expr2._functor &&
            expr1._arity === expr2._arity) {
            for (var i=0; i<expr1._arity; i++)
                if (!(expr1._args[i].equal(expr2._args[i])))
                    return false;
            return true;
        }
        return false;
    }

    Expression.prototype.contains = function(expr2) {
        var expr1 = this;

        if (expr1.equal(expr2))
            return true;

        for (var i=0; i<expr1._arity; i++) {
            if (expr1._args[i].contains(expr2))
                return true;
        }

        return false;
    }

    /**
     *  Succeed when the value can be computed.
     */
    Expression.prototype.can_compute_value = function() {
        for (var i=0; i<this._arity; i++) {
            if (!this._args[i].can_compute_value())
                return false;
        }
        return true;
    }

    Expression.prototype.arg = function(n) {
        if (n === undefined)
            n = 1;

        if (this._arity < n)
            throw 'cap.Expression.arg: not enough arguments for ' + n;
        return this._args[n-1];
    }

    Expression.prototype.lhs = function() {
        if (this._arity != 2)
            throw 'Error: no lhs when arity unequal to 2';
        return this._args[0];
    }

    Expression.prototype.rhs = function() {
        if (this._arity != 2)
            throw 'Error: no rhs when arity unequal to 2';
        return this._args[1];
    }

    Expression.prototype.variables = function() {
        var expr = this;
        var vars = [];

        for (var i=0; i<expr._arity; i++) {
            var arg = expr._args[i];

            vars = vars.concat(arg.variables());
        }

        return cap.remove_duplicates(vars);
    }

    Expression.prototype.traverse = function(func) {
        for (var i=0; i<this._arity; i++) {
            var arg = this._args[i];

            func.call(arg);
            if (arg instanceof Expression)
                arg.traverse(func);
        }
    }

    Expression.prototype.unify = function(expr2) {
        var expr1 = this;
        var i;

        if (expr2 instanceof Any)
            return expr2.unify(expr1);

        if (expr2 instanceof Expression &&
            expr1._arity === expr2._arity &&
            expr1._functor === expr2._functor) {
            var rval = true;

            for (i=0; i<expr1._arity; i++) {
                var arg1 = expr1._args[i];
                var arg2 = expr2._args[i];

                if (arg1.unify(arg2))
                    continue;
                rval = false;
                break;
            }
        }

        return rval;
    }

    Expression.prototype.deunify = function() {
        var i;

        for (i=0; i<this._arity; i++) {
            this._args[i].deunify();
        }

        return this;
    }

    /** Internal only.
     */
    Expression.prototype.replace_self = function(expr2) {
        var expr1 = this;

        expr1._functor = expr2._functor;
        expr1._arity = expr2._arity;
        for (var arg, i=0; i<expr2._args.length, arg=expr2._args[i]; i++) {
            expr1._args[i] = arg;
        }

        return this;
    }

    /**
     *  Recursively substitutes every occurrence of old in this expression by
     *  new_expr.
     */
    Expression.prototype.substitute = function(old, new_expr) {
        for (var arg, i=0; i<this._args.length, arg=this._args[i]; i++) {
            if (arg.equal(old))
                this._args[i] = new_expr;
            else
                if (arg instanceof Expression)
                    arg.substitute(old, new_expr);
        }

        return this;
    }

    /**
     *  Replace an existing argument by a new value.  If ith is given that
     *  argument is replaced, otherwise the argument is determine through a
     *  look up.  INTERNAL ONLY.
     */
    Expression.prototype.replace_arg = function(old, new_expr, ith) {
        var i;

        if (ith === undefined) {
            for (i=0; i<this._args.length; i++) {
                if (this._args[i] === old) {
                    ith = i;
                    break;
                }
            }
        }

        if (ith === undefined) {
            throw 'Error: replace_arg ' + old + ' not found';
        }

        this._args[ith] = new_expr;

        return this;
    }

    Expression.prototype.unify_bindings = function() {
        var i;

        for (i=0; i<this._arity; i++) {
            var arg = this._args[i];

            if (arg instanceof Any) {
                this._args[i] = arg.binding().copy();
                continue;
            }
            if (arg instanceof Expression)
                arg.unify_bindings();
        }

        return this;
    }

    Expression.prototype.copy_unified = function() {
        var cp = new Expression(this._functor, this._args);

        for (var i=0; i<cp._arity; i++) {
            var arg = cp._args[i];

            if (arg instanceof Any)
                cp._args[i] = arg.binding().copy();
            else
                cp._args[i] = arg.copy();
        }

        return cp;
    }


    /*------------------------------------------------------------
     *  Negate: negative
     *------------------------------------------------------------*/

    Negate = cap.Negate = function(expr) {
        Expression.call(this, '-', expr);

        return this;
    }

    ist.extend(Negate, Expression);

    Negate.prototype.toString = function() {
        return '-(' + this._args[0] + ')';
    }

    Negate.prototype.copy = function(unify) {
        return new Negate(this._args[0].copy(unify));
    }

    Negate.prototype.value = function() {
        if (!this._value && this.can_compute_value())
            this._value = -(this._args[0]);
        return this._value;
    }

    /*------------------------------------------------------------
     *  Identity: single argument is the real expression
     *------------------------------------------------------------*/

    Identity = cap.Identity = function(expr) {
        Expression.call(this, 'identity', expr);

        return this;
    }

    ist.extend(Identity, Expression);

    Identity.prototype.toString = function() {
        return 'Identity(' + this._args[0] + ')';
    }

    Identity.prototype.copy = function(bool) {
        return this._args[0].copy(bool);
    }


    /*------------------------------------------------------------
     *  Equation: equations
     *------------------------------------------------------------*/

    Equation = cap.Equation = function(lhs, rhs) {
        Expression.call(this, '=', lhs, rhs);

        return this;
    }

    ist.extend(Equation, Expression);

    Equation.prototype.solve = function(v) {
        var eq = this;
        var rhs = eq.isolate_lhs(v);

        if (rhs) {
            var value = rhs.evaluate();

            if (value !== null) {
                v.value(value);
                return true;
            }
            return false;
        }
        return false;
    }

    Equation.prototype.copy = function(unify) {
        return new Equation(this.lhs().copy(unify), this.rhs().copy(unify));
    }

    Equation.prototype.isolate_lhs = function(v) {
        var exprs = this.isolate(v);

        if (exprs.length > 1)
            ist.printf('Warning: cap.isolate_lhs ' + v + ': multiple expressions found');

        for (var i=0; i<exprs.length; i++) {
            var expr = exprs[i];
            if (expr.lhs().equal(v))
                return expr.rhs();
        }
        return null;
    }

    /** Isolate variable v in this equation.  The return value is an array
     *  of expressions in which v is isolated.  An empty array is returned
     *  if the variable could not be isolated.
     */
    Equation.prototype.isolate = function(v) {
        var x = new Contains('x', v);
        var y = new Contains('y', v);
        var a = new Uncontains('a', v);
        var b = new Uncontains('b', v);

        var rval = [];
        var current = [this.copy()];
        var found = [];

        while (current.length > 0 || found.length > 0) {
            //  Add new equations found in previous cycle to current.
            for (var i=0; i<found.length; i++)
                current.push(found[i]);
            found = [];

            //  Solve the remaining equations
            for (var i=0; i<current.length; i++) {
                var eq = current[i];

                do {
                    var state = { changed: false, count: 0 };

                    eq.simplify();

                    // a = b / x --> x = b / a
                    eq = eq.rewrite(equation(a, divide(b,x)),
                                        equation(x, divide(b,a)), state);

                    // a = x --> x = a
                    eq = eq.rewrite(equation(a,x),
                                        equation(x,a), state);

                    // a + b = x --> x = a + b
                    eq = eq.rewrite(equation(add(a,b), x),
                                        equation(x, add(a,b)), state);

                    // a + x = b --> x = b - a
                    eq = eq.rewrite(equation(add(a,x), b),
                                        equation(x, subtract(b,a)), state);

                    // x + a = b --> x = b - a
                    eq = eq.rewrite(equation(add(x,a), b),
                                        equation(x, subtract(b,a)), state);

                    // a = b + x --> x = b - a
                    eq = eq.rewrite(equation(a, add(b,x)),
                                        equation(x, subtract(a,b)), state);

                    // a = x + b --> x = b - a
                    eq = eq.rewrite(equation(a, add(x,b)),
                                        equation(x, subtract(a,b)), state);

                    // a - b = x --> x = a - b
                    eq = eq.rewrite(equation(subtract(a,b), x),
                                        equation(x, subtract(a,b)), state);

                    // a - x = b --> x = a - b
                    eq = eq.rewrite(equation(subtract(a,x), b),
                                        equation(x, subtract(a,b)), state);

                    // x - a = b --> x = a + b
                    eq = eq.rewrite(equation(subtract(x,a), b),
                                        equation(x, add(a,b)), state);

                    // a = b - x --> x = b - a
                    eq = eq.rewrite(equation(a, subtract(b,x)),
                                        equation(x, subtract(b,a)), state);

                    // a = x - b --> x = a + b
                    eq = eq.rewrite(equation(a, subtract(x,b)),
                                        equation(x, add(a,b)), state);

                    // a * b = x --> x = a * b
                    eq = eq.rewrite(equation(multiply(a,b), x),
                                        equation(x, multiply(a,b)), state);

                    // a * x = b --> x = b / a
                    eq = eq.rewrite(equation(multiply(a,x), b),
                                        equation(x, divide(b,a)), state);

                    // x * a = b --> x = b / a
                    eq = eq.rewrite(equation(multiply(x,a), b),
                                        equation(x, divide(b,a)), state);

                    // a = b * x --> x = a / b
                    eq = eq.rewrite(equation(a, multiply(b,x)),
                                        equation(x, divide(a,b)), state);

                    // a = x * b --> x = b / a
                    eq = eq.rewrite(equation(a, multiply(x,b)),
                                        equation(x, divide(b,a)), state);

                    // a / b = x --> x = a / b
                    eq = eq.rewrite(equation(divide(a,b), x),
                                        equation(x, divide(a,b)), state);

                    // a / x = b --> x = a / b
                    eq = eq.rewrite(equation(divide(a,x), b),
                                        equation(x, divide(a,b)), state);

                    // x / a = b --> x = a * b
                    eq = eq.rewrite(equation(divide(x,a), b),
                                        equation(x, multiply(a,b)), state);

                    // a = b / x --> x = b / a
                    eq = eq.rewrite(equation(a, divide(b,x)),
                                        equation(x, divide(b,a)), state);

                    // a = x / b --> x = a * b
                    eq = eq.rewrite(equation(a, divide(x,b)),
                                        equation(x, multiply(a,b)), state);

                    // x ** 2 = a --> x = sqrt(a)	
                    // x ** 2 = a --> x = - sqrt(a)
                    var power2 = equation(power(x,2), a);

                    if (eq.unify(power2)) {
                        var eq2 = eq.copy();

                        power2.deunify();
                        eq = eq.rewrite(power2,
                                        equation(x, sqrt(a)), state);
                        found.push(eq2.rewrite(power2,
                                               equation(x, negate(sqrt(a))), state));
                    }

                    // x ** a = b --> x = b ** 1/a
                    eq = eq.rewrite(equation(power(x,a), b),
                                        equation(x, power(b,divide(1,a)), state, true));

                    // a = x ** b --> x ** b = a
                    eq = eq.rewrite(equation(a, power(x,b)),
                                        equation(power(x,b), a), state);

                    // x ** 1/a = b --> x = b ** a
                    eq = eq.rewrite(equation(power(x,divide(1,a)), b),
                                        equation(x, power(b,a)), state);

                    // sqrt(x) = a
                    eq = eq.rewrite(equation(sqrt(x), a),
                                        equation(x, power(a,2)), state);

                    // sin(x) = a --> x = asin(a)
                    eq = eq.rewrite(equation(sin(x), a),
                                        equation(x, asin(a)), state);

                    // cos(x) = a --> x = acos(a)
                    eq = eq.rewrite(equation(cos(x), a),
                                        equation(x, acos(a)), state);

                    // tan(x) = a --> x = atan(a)
                    eq = eq.rewrite(equation(tan(x), a),
                                        equation(x, atan(a)), state);

                } while (state.count > 0);

                //  Solved the current
                rval.push(eq);
            }
            current = [];
        }

        return rval;
    }

    Expression.prototype.simplify = function() {
        var expr = this;

//      printf('SIMPLIFY ' + expr);

        var a = any('a');
        var b = any('b');
        var c = any('c');
        var one = number(1);
        var zero = number(0);
        var i = any_number('i');
        var j = any_number('j');

        var rules = [
            { from: power(1,a),
              to: 1
            },
/*          { from: power(negate(1),a),
              to:   1,
              condition: is_even(a)
            },
            { from: power(negate(1),a),
              to:   1,
              condition: is_odd(a)
            },
*/
            { from: power(0,a),
              to: 0
            },
            { from: power(a,0),
              to: 1
            },
            { from: power(a,1),
              to: a
            },
            { from: power(a,add(b,c)),
              to: multiply(power(a,b), power(a,c))
            },
            { from: divide(power(a,i), power(a,j)),
              to: power(a,subtract(i,j))
            },
            { from: divide(divide(a,b), c),
              to: divide(a, multiply(b,c))
            },
            { from: divide(a, divide(b,c)),
              to: divide(multiply(a,c), b)
            },
            { from: add(i,j),
              to: 'evaluate'
            },
            { from: subtract(i,j),
              to: 'evaluate'
            },
            { from: multiply(i,j),
              to: 'evaluate'
            },
            { from: divide(add(a,b), c),
              to: add(divide(a,c), divide(b,c))
            },
            { from: add(a, 0),
              to: a
            },
            { from: add(0, a),
              to: a
            },
            { from: add(a, a),
              to: multiply(a, 2)
            },
            { from: subtract(0,a),
              to: negate(a)
            },
            { from: subtract(a,0),
              to: a
            },
            { from: subtract(a,a),
              to: zero
            },
            { from: divide(a,a),
              to: one,
              constraint: 'a not equal to zero'
            },
            { from: divide(a,1),
              to: a
            },
            { from: multiply(a,1),
              to: a
            },
            { from: multiply(1,a),
              to: a
            },
            { from: multiply(0,a),
              to: zero
            },
            { from: multiply(a,0),
              to: zero
            }
        ];

        expr = simplify(expr, rules);

//      printf('  SIMPLIFIED ' + expr);

        return expr;

        function simplify(expr, rules) {
            var state = { change: false, count: 0 };
            var i;

            for (i=0; i<rules.length; i++) {
                var rule = rules[i];

                expr = expr.rewrite(rule.from, rule.to, state);
            }

            for (i=0; i<expr._arity; i++) {
                if (expr._args[i] instanceof Expression) {
                    expr._args[i] = simplify(expr._args[i], rules, state);
                }
            }

            if (state.count > 0)
                return simplify(expr, rules);

            return expr;
        }
    }

    /*------------------------------------------------------------
     *  Add
     *------------------------------------------------------------*/

    Add = cap.Add = function(lhs, rhs) {
        Expression.call(this, '+', lhs, rhs);
        return this;
    }

    ist.extend(Add, Expression);

    Add.prototype.copy = function(unify) {
        return new Add(this.lhs().copy(unify), this.rhs().copy(unify));
    }

    Add.prototype.evaluate = function() {
        var lhs = this.lhs().evaluate();
        var rhs = this.rhs().evaluate();
        var qs = coerce_to_quantities(lhs, rhs);
        var unit;

        if (qs === null)
            return null;

        if (qs.unit1 === qs.unit2)
            unit = qs.unit1;
        else {
            ist.printf('Add.evaluate() incompatible units: ' + qs.unit1 + ' ' + qs.unit2);
            return null;
        }

        var value = qs.value1 + qs.value2;

        if (unit === null)
            return value;

        return new Quantity({value: value,
                             unit: unit
                            });
    }

    Add.prototype.value = function() {
        return this._value;
    }


    /*------------------------------------------------------------
     *  Sine
     *------------------------------------------------------------*/

    Sine = cap.Sine = function(arg) {
        Expression.call(this, 'sin', arg);
        return this;
    }

    ist.extend(Sine, Expression);

    Sine.prototype.copy = function(unify) {
        return new Sine(this.arg().copy(unify));
    }

    Sine.prototype.evaluate = function() {
        var arg = this.arg().evaluate();

        if (arg !== null)
            return Math.sin(arg);
        throw 'cap.Sine.evaluate: argument invalid: ' + this.arg();
    }

    Sine.prototype.value = function() {
        throw 'cap.Sine.value: not defined, use .evaluate()';
    }


    /*------------------------------------------------------------
     *  Cosine
     *------------------------------------------------------------*/

    Cosine = cap.Cosine = function(arg) {
        Expression.call(this, 'cos', arg);
        return this;
    }

    ist.extend(Cosine, Expression);

    Cosine.prototype.copy = function(unify) {
        return new Cosine(this.arg().copy(unify));
    }

    Cosine.prototype.evaluate = function() {
        var arg = this.arg().evaluate();

        if (arg !== null)
            return Math.cos(arg);
        throw 'cap.Cosine.evaluate: argument invalid: ' + this.arg();
    }

    Cosine.prototype.value = function() {
        throw 'cap.Cosine.value: not defined, use .evaluate()';
    }


    /*------------------------------------------------------------
     *  Tangent
     *------------------------------------------------------------*/

    Tangent = cap.Tangent = function(arg) {
        Expression.call(this, 'tan', arg);
        return this;
    }

    ist.extend(Tangent, Expression);

    Tangent.prototype.copy = function(unify) {
        return new Tangent(this.arg().copy(unify));
    }

    Tangent.prototype.evaluate = function() {
        var arg = this.arg().evaluate();

        if (arg !== null)
            return Math.tan(arg);
        throw 'cap.Tangent.evaluate: argument invalid: ' + this.arg();
    }

    Tangent.prototype.value = function() {
        throw 'cap.Tangent.value: not defined, use .evaluate()';
    }


    /*------------------------------------------------------------
     *  ArcSine
     *------------------------------------------------------------*/

    ArcSine = cap.ArcSine = function(arg) {
        Expression.call(this, 'asin', arg);
        return this;
    }

    ist.extend(ArcSine, Expression);

    ArcSine.prototype.copy = function(unify) {
        return new ArcSine(this.arg().copy(unify));
    }

    ArcSine.prototype.evaluate = function() {
        var arg = this.arg().evaluate();

        if (arg !== null) {
            var rad = Math.asin(arg);

            if (rad !== NaN)
                return rad * 180 / pi;
            throw 'cap.ArcSine.evaluate: no value defined for ' + arg;
        }
        throw 'cap.ArcSine.evaluate: argument invalid: ' + this.arg();
    }

    ArcSine.prototype.value = function() {
        throw 'cap.ArcSine.value: not defined, used .evaluate()';
    }


    /*------------------------------------------------------------
     *  ArcCosine
     *------------------------------------------------------------*/

    ArcCosine = cap.ArcCosine = function(arg) {
        Expression.call(this, 'acos', arg);
        return this;
    }

    ist.extend(ArcCosine, Expression);

    ArcCosine.prototype.copy = function(unify) {
        return new ArcCosine(this.arg().copy(unify));
    }

    ArcCosine.prototype.evaluate = function() {
        var arg = this.arg().evaluate();

        if (arg !== null) {
            var rad = Math.acos(arg);

            if (rad !== NaN)
                return rad * 180 / pi;
            throw 'cap.ArcCosine.evaluate: no value defined for ' + arg;
        }
        throw 'cap.ArcCosine.evaluate: argument invalid: ' + this.arg();
    }

    ArcCosine.prototype.value = function() {
        throw 'cap.ArcCosine.value: not defined, used .evaluate()';
    }


    /*------------------------------------------------------------
     *  ArcTangent
     *------------------------------------------------------------*/

    ArcTangent = cap.ArcTangent = function(arg) {
        Expression.call(this, 'atan', arg);
        return this;
    }

    ist.extend(ArcTangent, Expression);

    ArcTangent.prototype.copy = function(unify) {
        return new ArcTangent(this.arg().copy(unify));
    }

    ArcTangent.prototype.evaluate = function() {
        var arg = this.arg().evaluate();

        if (arg !== null) {
            var rad = Math.atan(arg);

            if (rad !== NaN)
                return rad * 180 / pi;
            throw 'cap.ArcTangent.evaluate: no value defined for ' + arg;
        }
        throw 'cap.ArcTangent.evaluate: argument invalid: ' + this.arg();
    }

    ArcTangent.prototype.value = function() {
        throw 'cap.ArcTangent.value: not defined, use .evaluate()';
    }


    /*------------------------------------------------------------
     *  Subtract
     *------------------------------------------------------------*/

    Subtract = cap.Subtract = function(lhs, rhs) {
        Expression.call(this, '-', lhs, rhs);
        return this;
    }

    ist.extend(Subtract, Expression);

    Subtract.prototype.copy = function(unify) {
        return new Subtract(this.lhs().copy(unify), this.rhs().copy(unify));
    }

    Subtract.prototype.evaluate = function() {
        var lhs = this.lhs().evaluate();
        var rhs = this.rhs().evaluate();
        var qs = coerce_to_quantities(lhs, rhs);
        var unit;

        if (qs === null)
            return null;

        if (qs.unit1 === qs.unit2)
            unit = qs.unit1;
        else
            throw 'cap.Subtract.evaluate(): incompatible units ' + qs.unit1 + ' ' + qs.unit2;

        if (unit === null)
            return qs.value1 - qs.value2;

        return new Quantity({value: qs.value1 - qs.value2,
                             unit: unit
                            });
    }

    Subtract.prototype.value = function() {
        return this._value;
    }


    /*------------------------------------------------------------
     *  Multiply
     *------------------------------------------------------------*/

    Multiply = cap.Multiply = function(lhs, rhs) {
        Expression.call(this, '*', lhs, rhs);
        return this;
    }

    ist.extend(Multiply, Expression);

    Multiply.prototype.copy = function(unify) {
        return new Multiply(this.lhs().copy(unify), this.rhs().copy(unify));
    }

    Multiply.prototype.evaluate = function() {
        var lhs = this.lhs().evaluate();
        var rhs = this.rhs().evaluate();
        var qs = coerce_to_quantities(lhs, rhs);
        var unit;

        if (qs === null)
            return null;

        if (qs.unit1 && qs.unit2) // TBD
            unit = simplify_unit(qs.unit1 + '*' + qs.unit2);
        else
            if (qs.unit1)
                unit = qs.unit1;
        else
            if (qs.unit2)
                unit = qs.unit2;
        else
            unit = null;

        if (unit === null)
            return qs.value1 * qs.value2;

        return new Quantity({value: qs.value1 * qs.value2,
                             unit: unit
                            });
    }

    Multiply.prototype.value = function() {
        return this._value;
    }


    /*------------------------------------------------------------
     *  Power
     *------------------------------------------------------------*/

    Power = cap.Power = function(lhs, rhs) {
        Expression.call(this, '**', lhs, rhs);
        return this;
    }

    ist.extend(Power, Expression);

    Power.prototype.latex = function() {
        return this.lhs().latex() + '^{' + this.rhs() + '}';
    }

    Power.prototype.copy = function(unify) {
        return new Power(this.lhs().copy(unify), this.rhs().copy(unify));
    }

    Power.prototype.evaluate = function() {
        var lhs = this.lhs().evaluate();
        var rhs = this.rhs().evaluate();
        var qs = coerce_to_quantities(lhs, rhs);
        var unit;

        if (qs === null)
            return null;

        unit = qs.unit1;

        var val = Math.pow(qs.value1, qs.value2);

        if (unit === null)
            return val;

        if (qs.unit1 && qs.value2)
            unit = simplify_unit(qs.unit1 + '^' + qs.value2);
        else
            unit = qs.unit1 + '^' + qs.value2;

        return new Quantity({ value: val,
                              unit: unit
                            });
    }

    Power.prototype.value = function() {
        if (!this._value && this.can_compute_value())
            this._value = this.lhs() * this.rhs();
        return this._value;
    }


    /*------------------------------------------------------------
     *  Square root
     *------------------------------------------------------------*/

    Sqrt = cap.Sqrt = function(arg) {
        Expression.call(this, 'sqrt', arg);
        return this;
    }

    ist.extend(Sqrt, Expression);

    Sqrt.prototype.latex = function() {
        return '\\sqrt{' + this.arg().latex() + '}';
    }

    Sqrt.prototype.copy = function(unify) {
        return new Sqrt(this.arg().copy(unify));
    }

    Sqrt.prototype.evaluate = function() {
        var arg = this.arg();
        var value = null;
        var unit = null;

        if (arg instanceof Quantity) {
            value = arg.evaluate();
            unit = arg.unit();
        } else
            value = arg.evaluate();

        var val = Math.sqrt(value);

        if (unit === null)
            return val;

        unit = simplify_unit('sqrt(' + unit + ')');

        return new Quantity({ value: val,
                              unit: unit
                            });
    }

    Sqrt.prototype.value = function() {
        if (!this._value && this.can_compute_value())
            this._value = this.lhs() * this.rhs();
        return this._value;
    }


    /*------------------------------------------------------------
     *  Divide
     *------------------------------------------------------------*/

    Divide = cap.Divide = function(lhs, rhs) {
        Expression.call(this, '/', lhs, rhs);
        return this;
    }

    ist.extend(Divide, Expression);

    Divide.prototype.latex = function() {
        return '\\frac{' + this._args[0].latex() + '}{' + this._args[1].latex() + '}';
    }

    Divide.prototype.copy = function(unify) {
        return new Divide(this.lhs().copy(unify), this.rhs().copy(unify));
    }

    function coerce_to_quantities(lhs, rhs) {
        if (lhs === null || rhs === null)
            return null;

        if (typeof(lhs) === 'number' && typeof(rhs) === 'number')
            return { value1: lhs, unit1: null,
                     value2: rhs, unit2: null
                   };

        var value1 = null;
        var value2 = null;
        var unit1 = null;
        var unit2 = null;

        if (lhs instanceof Quantity) {
            value1 = lhs.value();
            unit1 = lhs.unit();
        } else
            if (typeof(lhs) === 'number')
                value1 = lhs;

        if (rhs instanceof Quantity) {
            value2 = rhs.value();
            unit2 = rhs.unit();
        } else
            if (typeof(rhs) === 'number')
                value2 = rhs;

        if (value1 === null || value2 === null)
            return null;

        return { value1: value1,
                 unit1: unit1,
                 value2: value2,
                 unit2: unit2
               };
    }

    Divide.prototype.evaluate = function() {
        var lhs = this.lhs().evaluate();
        var rhs = this.rhs().evaluate();
        var qs = coerce_to_quantities(lhs, rhs);
        var unit;

/*
        printf('Divide.evaluate');
        printf('  expr ' + this);
        printf('  lhs  ' + lhs);
        printf('  rhs  ' + rhs);
*/
        if (qs === null)
            return null;

        if (qs.value2 === 0)
            return NaN;

        if (qs.unit1 && qs.unit2) { // TBD
            unit = simplify_unit(qs.unit1 + '/' + qs.unit2);
        }
        else
            if (qs.unit1)
                unit = qs.unit1;
        else
            if (qs.unit2)
                unit = simplify_unit('1/' + qs.unit2);
        else
            unit = null;

        var value = qs.value1 / qs.value2;

        if (unit === null)
            return value;

        return new Quantity({value: value,
                             unit: unit
                            });
    }


    /*------------------------------------------------------------
     *  Literal: abstract class for all literals (variables, numbers, ...)
     *------------------------------------------------------------*/

    Literal = cap.Literal = function() {
        Base.call(this);

        this._value = null;
        this._parent = null;

        return this;
    }

    ist.extend(Literal, Base);

    Literal.prototype.unify = function(l2) {
        if (l2 instanceof Any)
            return l2.unify(this);
        return this.equal(l2);
    }

    Literal.prototype.variables = function() {
        return [];
    }


    /*------------------------------------------------------------
     *  Number: class for numbers (precision currently depends on native numbers)
     *------------------------------------------------------------*/

    Number = cap.Number = function(val) {
        if (!(typeof(val) === 'number'))
            throw 'Error: Number ' + val + ' is not a number';

        Literal.call(this);
        this._value = val;

        return this;
    }

    ist.extend(Number, Literal);

    Number.prototype.toString = function() {
        return this._value;
    }

    Number.prototype.latex = function() {
        return this._value;
    }

    Number.prototype.copy = function() {
        return new Number(this._value);
    }

    Number.prototype.equal = function(expr) {
        if (expr instanceof Number &&
            this._value === expr._value)
            return true;
        return false;
    }

    Number.prototype.evaluate = function() {
/*
        printf('Number.evaluate');
        printf('  expr ' + this);
        printf('  value' + this._value);
*/

        return this._value;
    }

    Number.prototype.can_compute_value = function() {
        return true;
    }


    /*------------------------------------------------------------
     *  Any: logical variables
     *------------------------------------------------------------*/

    Any = cap.Any = function(name, bind) {
        Base.call(this);

        this._name = (name === undefined ? '_' : name);
        this._binding = (bind === undefined ? null : bind);

        return this;
    }

    ist.extend(Any, Base);

    Any.prototype.toString = function() {
        if (this._binding)
            return 'any(' + this._name + '=' + this._binding + ')';
        return 'any(' + this._name + ')';
    }

    Any.prototype.copy = function(unify) {
        if (unify) {
            if (this._binding)
                return this._binding.copy();
            throw 'Error: trying to unify ' + this + ' with no binding';
        }
        return new Any(undefined, this._binding);
    }

    Any.prototype.unify = function(expr) {
        if (this._binding === null) {
            this._binding = expr;
            return true;
        }
//      printf('ANY HAS BINDING ' + this._binding);
//      printf('  TRYING ' + expr);
        return this._binding.equal(expr);
    }

    Any.prototype.deunify = function() {
        this._binding = null;
    }


    /*------------------------------------------------------------
     *  AnyNumber: logical variable matching any number
     *------------------------------------------------------------*/

    AnyNumber = cap.AnyNumber = function(name, bind) {
        Any.call(this, name, bind);

        return this;
    }

    ist.extend(AnyNumber, Any);

    AnyNumber.prototype.unify = function(expr) {
        if (expr instanceof Number) {
            this._binding = expr;
//          printf('  --- unify: ' + this + '   ' + this._binding);
            return true;
        }
        return false;
    }


    /*------------------------------------------------------------
     *  Contains: logical variable matching sub-expression containing another expression
     *------------------------------------------------------------*/

    Contains = cap.Contains = function(name, sub, bind) {
        Any.call(this, name, bind);

        this._content = sub;

        return this;
    }

    ist.extend(Contains, Any);

    Contains.prototype.unify = function(expr) {
        if (this._binding === null) {
            if (expr.contains(this._content)) {
                this._binding = expr;
                return true;
            }
            return false;
        }
        return this._binding.equal(expr);
    }


    /*------------------------------------------------------------
     *  Uncontains: logical variable matching expressions *not*
     *  containing a sub-expression.
     *------------------------------------------------------------*/

    Uncontains = cap.Uncontains = function(name, sub, bind) {
        Any.call(this, name, bind);

        this._content = sub;

        return this;
    }

    ist.extend(Uncontains, Any);

    Uncontains.prototype.unify = function(expr) {
        if (this._binding === null) {
            if (!expr.contains(this._content)) {
                this._binding = expr;
                return true;
            }
            return false;
        }
        return this._binding.equal(expr);
    }


    /*------------------------------------------------------------
     *  EquationVariables (largely internal)
     *------------------------------------------------------------*/

    var EquationVariables = cap.EquationVariables = function(expr, eq, vars) {
        var evs = this;

        evs._expression = expr;
        evs._equation = eq;
        if (vars === undefined)
            evs._variables = expr.variables();
        else
            evs._variables = vars;

        return this;
    }

    EquationVariables.prototype.expression = function() {
        return this._expression;
    }

    EquationVariables.prototype.equation = function() {
        return this._equation;
    }

    EquationVariables.prototype.variables = function() {
        return this._variables;
    }


    /*------------------------------------------------------------
     *  System: a (named) system of equations
     *------------------------------------------------------------*/

    System = cap.System = function(name) {
        var sys = this;

        sys._id = id();
        sys._name = name;

        sys._variables = {}; // Indexed by ?
        sys._dependencies = {}; // Dependencies of the variables,
            // array of EquationVariables
        sys._equations = [];
        sys._views = {};
        sys._modified = false; // Equations have been added

        return sys;
    }

    System.prototype.clear_modifications = function() {
        var vs = this._variables;

        for (var p in vs) {
            vs[p].updated(false);
            vs[p].modified(false);
        }

        return this;
    }

    System.prototype.clear_locks = function() {
        var vs = this._variables;

        for (var p in vs) {
            vs[p].locked(false);
        }

        return this;
    }

    System.prototype.reset = function() {
        var sys = this;

        for (var p in sys._variables)
            sys._variables[p].value(null);
    }

    System.prototype.isolate_all = function() {
        var sys = this;
        var vs = sys._variables;
        var eqs = sys._equations;
        var deps = sys._dependencies = {};
        var p;

        //  Initialise dependencies for all variables
        for (p in vs)
            deps[p] = [];

        //  For all equations, isolate all variables
        for (var i=0; i<eqs.length; i++) {
            var eq = eqs[i];

            for (var p in vs) {
                var v = vs[p];

                if (eq.contains(v)) {
                    var expr = eq.isolate_lhs(v);

                    if (expr !== null)
                        deps[p].push(new EquationVariables(expr, eq));
                }
            }
        }

        return sys;
    }

    /**
     *  Returns an array of dependencies for variable v.  The array contains
     *  object literals of the form { equation: eq, variables: vars} where eq
     *  is an equation and vars the variables that are dependent on v.
     *
     *  @param {Variable} v	Variable for which to find dependencies
     *  @returns {Array} 	Array of dependencies as object literals
     */
    System.prototype.dependencies = function(v) {
        var sys = this;
        var vs = sys._variables;
        var eqs = sys._equations;
        var rval = [];

        for (var i=0; i<eqs.length; i++) {
            var eq = eqs[i];

            if (eq.contains(v)) {
                var deps = [];

                for (var p in vs) {
                    var w = vs[p];

                    if (v === w)
                        continue;
                    if (eq.contains(w))
                        deps.push(w);
                }
                if (deps.length > 0)
                    rval.push({
                        equation: eq,
                        variables: deps
                    });
            }
        }

        return rval;
    }

    System.prototype.add_view = function(v, fn) {
        var array = this._views[v.id()] || [];

        array.push(fn);
        this._views[v.id()] = array;

        return this;
    }

    System.prototype.update_views = function() {
        var p;

        for (p in this._variables) {
            var v = this._variables[p];
            var views = this._views[v.id()];

            if (views) {
                if (v._displayed === true) {
//                  printf('v ' + v.name() + ' not updated');
                    continue;
                }
                for (var i=0; i<views.length; i++) {
                    views[i].call(v);
                }
                v._displayed = true;
            }
        }

        return this;
    }

    System.prototype.id = function() {
        return this._id;
    }

    System.prototype.pp_status = function() {
        var sys = this;
        var vs = sys._variables;

        ist.printf('System status ');
        for (var p in vs) {
            var v = vs[p];
            ist.printf(v.symbol() + '  value=' + v.value() + ' m=' + v.modified() + ' l=' + v.locked() + ' c=' + v.constant() + ' u=' + v.updated());

        }
    }

    System.prototype.pp = function() {
        var vars = this._variables;
        var v;

        ist.printf('----------------');
        ist.printf('System ' + this._name);
        ist.printf('  Variables');
        for (v in vars) {
            var rv = vars[v];
            if (rv instanceof Property) {
                ist.printf('    ' + rv.id() + ' ' + rv.symbol() + ' = ' + rv.value() + '  [' + rv.role() + ']');


            } else
                ist.printf('    ' + rv.id() + ' ' + rv.symbol() + ' = ' + rv.value());
        }
        ist.printf('  --*--*--*--');
        ist.printf('  Equations');
        for (v=0; v<this._equations.length; v++) {
            ist.printf('    ' + this._equations[v]);
        }
        ist.printf('*** pp done ***\n');
    }

    System.prototype.add_variable = function(v) {
        var sys = this;

        if (!sys.lookup_variable(v))
            sys._variables[v.id()] = v;

        return sys;
    }

    System.prototype.variables = function() {
        return this._variables;
    }

    System.prototype.equations = function() {
        return this._equations;
    }

    /**
     * Add an equation to the system.  Any variables in the equation that are
     * already in the system are made to point to the same variable. 
     */
    System.prototype.add_equation = function(eq) {
        var sys = this;

        if (!eq instanceof Equation)
            throw 'cap.System.add_equation(): not an equation ' + eq;

        eq.traverse(function() {
            if (is_handle(this)) {
                if (!sys.lookup_variable(this.variable()))
                    sys.add_variable(this.variable());
            }
        });
        sys._equations.push(eq);
        sys._modified = true;

        return sys;
    }

    System.prototype.lookup_variable = function(v) {
        return this._variables[v.id()];
    }

    System.prototype.printf_status = function() {
        var vs = this._variables;
        var p;

        for (p in vs) {
            var v = vs[p];
            ist.printf(v + ' ' + v.value() + ' u=' + v.updated() + ' m=' + v.modified() + ' ' + v.role());

        }
    }


    System.prototype.solve_modified = function(v) {
        if (v.updated()) {
            v.modified(false);
            return;
        }

        var sys = this;
        var deps = sys.dependencies(v);
        var i, j;

        v.modified(false);
        v.updated(true); // Prevent cycles

        for (i=0; i<deps.length; i++) {
            var eq = deps[i].equation;
            var vs = deps[i].variables;

            if (vs.length < 1)
                continue;

            //  Only one dependent variable in this equation.
            //  Compute new value of the variable, and set modified flag.
            if (vs.length === 1) {
                sys.solve_single(eq, vs[0]);
                continue;
            }

     //  Multiple dependent variables.  Find one that needs to be computed.
            for (j=0; j<vs.length; j++) {
                var w = vs[j];
                if (w.role() === 'input' || w.role() === 'constant' || w.role() === 'i/o')
                    continue;
                sys.solve_single(eq, w);
                break;
            }
        }
    }

    System.prototype.solve_single = function(eq, v) {
        var rhs = eq.isolate_lhs(v);

        if (rhs) {
            var value = rhs.evaluate();

            if (value !== null) {
                v.value(value);
                v.modified(true);
//              v.updated(true);
            }
        } else {
            ist.printf('*** Could not isolate ' + v + ' in ' + eq);
            v.updated(true);
        }
    }

    System.prototype.evaluate_equation = function(base_eq, v) {
        var sys = this;
        var deps = sys._dependencies;
        var evs = deps[v.id()];

        for (var i=0; i<evs.length; i++) {
            var ev = evs[i];

            if (ev.equation() === base_eq) {
                var val = ev.expression().evaluate();
                v.value(val);
            }
        }

        return sys;
    }

    System.prototype.solve_unknowns = function(prev) {
        var sys = this;
        var unknowns = [];
        var vs = sys._variables;
        var deps = sys._dependencies;

        for (var p in vs) {
            var v = vs[p];

            if (v.unknown())
                unknowns.push(v);
        }

        if (unknowns.length === 0)
            return sys;

        if (prev) {
            var identical = true;

            for (var x=0; x<prev.length; x++) {
                if (prev[x] === unknowns[x])
                    continue;
                identical = false;
            }
            if (identical) {
                sys.pp_status();
                throw 'Error: loop in System.solve_unknowns';
            }
        }

        for (var i=0; i<unknowns.length; i++) {
            var v = unknowns[i];

            if (!v.unknown())
                continue;

            var evs = deps[v.id()];

            for (var j=0; j<evs.length; j++) {
                var ev = evs[j];
                var vs2 = ev.variables();
                var can_compute = true;

                for (var k=0; k<vs2.length; k++) {
                    if (vs2[k].unknown()) {
                        can_compute = false;
                        break;
                    }
                }
                if (can_compute) {
                    v.value(ev.expression().evaluate());
                    break;
                }
            }
        }

        return sys.solve_unknowns(unknowns);
    }

    System.prototype.initialise = function() {
        var sys = this;

        if (sys._modified) {
            sys.isolate_all();
            sys._modified = false;
        }

        return sys;
    }

/*
    System.prototype.evaluate = function(v0) {
        var sys = this;
        var v = sys.variable(v0);

        sys.initialise();

        var deps = sys._dependencies;
        var evs = deps[v.id()];
        
        for (var i=0; i<evs.length; i++) {
            var ev = evs[i];
            var eq = ev._equation;
            var value = evaluate
            
    }
*/

    System.prototype.variable = function(v) {
        var sys = this;
        var vs = this._variables;

        if (typeof(v) === 'string') {
            for (var p in vs) {
                if (vs[p].name() === v || vs[p].symbol() === v)
                    return vs[p];
            }
            return null;
        }

        if (v instanceof Variable)
            return vs[v.id()] ? v : null;

        return null;
    }

    System.prototype.solve = function() {
        var sys = this;

        //  Initialise
        if (sys._modified) {
            sys.isolate_all();
            sys._modified = false;
        }

        sys.solve_unknowns();

        var vs = sys._variables;
        var mods = [];

        for (var p in vs) {
            if (vs[p].modified() === true) {
                if (vs[p].locked())
                    throw 'cap.System.solve: variable ' + vs[p].symbol() + ' is both modified and locked';
                mods.push(vs[p]);
            }
        }

        solve_using_dependencies(mods);
        sys.clear_modifications();

        return sys;

        function solve_using_dependencies(mods) {
            var new_mods = [];
            var deps = sys._dependencies;

            for (var k=0; k<mods.length; k++) {
                var v = mods[k];
                var evs = deps[v.id()];

                if (v.locked())
                    throw 'cap.solve_using_dependencies(): trying to update locked variable ' + v.symbol();

                for (var i=0; i<evs.length; i++) {
                    var ev = evs[i];
                    var updatable = [];

                    for (var j=0; j<ev.variables().length; j++) {
                        var v2 = ev.variables()[j];

                        if (v2.is_modifiable() === false)
                            continue;
                        updatable.push(v2);
                    }

                    switch (updatable.length) {
                    case 0:
                        break;
                    case 1:
                        v2 = updatable[0];
                        sys.evaluate_equation(ev.equation(), v2);
                        v2.modified(true);
                        new_mods.push(v2);
                        break;
                    default:
                        ist.printf('---------------------------------');
                        ist.printf('DETAILED ERROR REPORT');
                        ist.printf('    variable ' + v.symbol());
                        ist.printf('    dependencies ' + updatable);
                        sys.pp_status();
                        throw 'cap.System.solve: too many dependencies';
                    }
                }
                v.modified(false);
                v.updated(true);
            }

            if (new_mods.length > 0)
                return solve_using_dependencies(new_mods);
        }
    }


    /*------------------------------------------------------------
     *  Variable: class for variables
     *------------------------------------------------------------*/

    Variable = cap.Variable = function(str, val) {
        assert_string(str);
        assert_optional_number(val);

        this._id = id();
        this._symbol = str;
        this._value = val === undefined ? null : val;
        this._role = null;
        this._modified = false; // Value has been modified
        this._locked = false; // Value is locked
        this._constant = false; // Cannot be modified
        this._updated = false; // Internal; intermediate in System.solve()
        this._direction = false;

        return this;
    }

    Variable.prototype.toString = function() {
        if (this._value === null)
            return this._symbol;
        return this._symbol + ':' + this._value;
    }

    Variable.prototype.latex = function() {
        return this._symbol;
    }

    //  Variable has been modified and the formula's in which it appears need
    //  to be updated.
    Variable.prototype.modified = function(bool) {
        if (bool === undefined)
            return this._modified;
        this._modified = bool;

        return this;
    }

    //  Variable is a constant.
    Variable.prototype.constant = function(bool) {
        if (bool === undefined)
            return this._constant;
        this._constant = bool;

        return this;
    }

    //  Variable is locked against modifications.
    Variable.prototype.locked = function(bool) {
        if (bool === undefined)
            return this._locked;
        this._locked = bool;

        return this;
    }

    //  Variable has been updated.  No need to recalculate it.
    Variable.prototype.updated = function(bool) {
        if (bool === undefined)
            return this._updated;
        this._updated = bool;

        return this;
    }

    //  Succeed when the variable can be modified given the status flags.
    Variable.prototype.is_modifiable = function() {
        var v = this;

        if (v._constant || v._modified || v._locked || v._updated)
            return false;

        return true;
    }

    //  Variable is displayed correctly (it has not been modified).
    Variable.prototype.displayed = function(bool) {
        if (bool === undefined)
            return this._displayed;
        this._displayed = bool;

        return this;
    }

    Variable.prototype.role = function(r) {
        var v = this;

        if (r === undefined)
            return v._role;
        v._role = r;
        return v;
    }

    Variable.prototype.id = function() {
        return this._id;
    }

    Variable.prototype.unknown = function() {
        return this._value === null;
    }

    Variable.prototype.copy = function() {
        return new Handle(this);
    }

    Variable.prototype.symbol = function() {
        return this._symbol;
    }

    Variable.prototype.evaluate = function() {
        return this._value;
    }

    Variable.prototype.value = function(val) {
        if (val === undefined)
            return this._value;

        if (val !== this._value) {
            this._value = val;
            this._displayed = false;
        }

        return this;
    }


    /*------------------------------------------------------------
     *  Property (sub-class of Variable for the time being)
     *------------------------------------------------------------*/

    Property = cap.Property = function(atts0) {
        if (atts0 === undefined)
            throw 'cap.Property: no argument specified';

        var atts = (typeof(atts0) === 'string' ? { symbol: atts0 } : atts0);
        var prop = this;
        var str = atts.name || atts.symbol;

        if (str === undefined)
            throw 'cap.Property: at least one of .name or .symbol must be given';

        Variable.call(this, str); // Or symbol, label?

        prop._thing = null;
        prop._range = null;
        prop._stops = null;
        prop._value = null;

        for (var p in atts) {
            if (p === 'quantity') {
                prop._value = new Quantity(atts.quantity);
                continue;
            }
            prop['_'+p] = atts[p];
        }

        if (prop._name === undefined)
            prop._name = prop._symbol;
        if (prop._symbol === undefined)
            prop._symbol = prop._name;

        return prop;
    }

    ist.extend(Property, Variable);

    Property.prototype.toString = function() {
        return '[P: ' + this._name + '/' + this._symbol + '=' + this.simple_value() +
        ']';
    }

    Property.prototype.latex = function() {
        return this._symbol;
    }

    Property.prototype.unknown = function() {
        if (this._value === null)
            return true;
        if (this._value instanceof Quantity)
            return this._value.unknown();
        return false;
    }

    Property.prototype.pp = function() {
        var prop = this;

        ist.printf('name:     ' + prop.name());
        ist.printf('symbol:   ' + prop.symbol());
        ist.printf('thing:    ' + prop.thing());
        ist.printf('range:    ' + prop.range());
        ist.printf('value:    ' + prop.value());

        return prop;
    }

    Property.prototype.thing = function(thing0) {
        if (thing0 === undefined)
            return this._thing;

        if (this._thing !== thing0) {
            this._thing = thing0;
            this._displayed = false;
        }
        return this;
    }

    Property.prototype.name = function(name0) {
        if (name0 === undefined)
            return this._name;

        if (this._name !== name0) {
            this._name = name0;
            this._displayed = false;
        }
        return this;
    }

    Property.prototype.symbol = function(sym0) {
        if (sym0 === undefined)
            return this._symbol;

        if (this._symbol !== sym0) {
            this._symbol = sym0;
            this._displayed = false;
        }
        return this;
    }

    Property.prototype.range = function() {
        return this._range;
    }

    Property.prototype.stops = function() {
        return this._stops;
    }

    Property.prototype.simple_value = function() {
        var value = this._value;

        if (value instanceof Quantity) {
            return value.value();
        }
        return value;
    }

    Property.prototype.value = function(val) {
        var prop = this;
        var value = prop._value;

        if (val === undefined) {
            return value;
        }

        prop._displayed = false;

//      printf('SETTING ' + prop.id() + ' ' + prop.name() + ' ' + val);

        if (value instanceof Quantity) {
            if (val === null) {
                value._value = null;
                return prop;
            }
            if (typeof(val) === 'number') {
                value._value = val;
                return prop;
            }
            if (val instanceof Quantity) {
                value._value = val._value;
                value._unit = val._unit;
                return prop;
            }
            if (val.value)
                value._value = val.value;
            if (val.unit)
                value._unit = val.unit;
            if (val.direction)
                value._direction = val.direction;
            return prop;
        }

        prop._value = val;

        return prop;
    };

    Property.prototype.unit_html = function() { // TBD
        var unit = this.unit();
        var value = this._value;

        if (value instanceof Quantity)
            return unit_html(value._unit);
        return null;
    }

    Property.prototype.unit = function(unit) {
        var prop = this;
        var value = prop._value;

        if (unit === undefined) {
            if (value instanceof Quantity)
                return value._unit;
            return null;
        }

        if (value instanceof Quantity) {
            if (value._unit !== unit) {
                value._unit = unit;
                prop._displayed = false;
            }
        }

        return prop;
    }

    Property.prototype.label = function(lab0) {
        var p = this;

        if (lab0 === undefined) {
            if (p._label !== undefined) {
                return p._label;
            }
            return p._name;
        }

        if (p._label !== lab0) {
            p._label = lab0;
            p._displayed = false;
        }

        return p;
    }


    /**
     *  Sets the value of a property from a controller with a fixed range
     *  (e.g., the position of a slider).  Argument is the controller range
     *  and value.  The real value is computed from the range of the property.
     *
     *  @param {Range} controller 	Range of the controller
     *  @returns {Property} this
     */
    Property.prototype.position_value = function(contr0) {
        var prop = this;
        var range = prop.range();

        if (range === null) {
            throw 'Error: cap.Property.position_value: no range for ' + prop.name();
            return prop;
        }

        var rmin = range.min;
        var rmax = range.max;
        var contr = contr0 || {};
        var cmin = contr.min === undefined ? 0 : contr.min;
        var cmax = contr.max === undefined ? 100 : contr.max;
        var cpos = contr.position === undefined ? 0 : contr.position;
        var stops = prop.stops();

        if (stops) {
            var pct_start = 0;
            var pct_end = 0;
            var clen = cmax - cmin;
            var cpct = (cpos / clen) * 100;

            for (var i=0; i<stops.length; i++) {
                pct_end += stops[i].percentage;

                if (cpct >= pct_start && cpct <= pct_end) {
                    var cstart = (pct_start * clen) / 100;
                    var cend = (pct_end * clen) / 100;
                    var cwidth = cend - cstart;
                    var coff = cpos - cstart;
                    var stop_start = stops[i].start;
                    var stop_end = stops[i].end;
                    var value = stop_start + (coff / cwidth) * (stop_end - stop_start);

                    prop.value(value);

                    return this;
                }
                pct_start = pct_end;
            }


            prop.value(rmax); //  Beyond range, set maximum

            return prop;
        }

        var method = range.method || 'linear';

        if (method === 'linear') {
            var factor = (cpos - cmin) / (cmax - cmin);
            var rvalue = rmin + factor * (rmax - rmin);

            prop.value(rvalue);

            return prop;
        }

        if (method === 'sine') {
            var rhalf = range.midpoint;
            var width = cmax - cmin;
            var rvalue;
            var start;

            rvalue = sine_position_value(rmin, rmax, rhalf, width, cpos);
            prop.value(rvalue);

            return prop;
        }

        throw 'cap.Property.position_value: method ' + method + ' not implemented';

        return prop;
    }

    Property.prototype.value_position = function(contr) {
        var prop = this;
        var cmin = contr.min === undefined ? 0 : contr.min;
        var cmax = contr.max === undefined ? 100 : contr.max;
        var range = prop.range();
        var rmin = range !== null ? range.min : cmin;
        var rmax = range !== null ? range.max : cmax;
        var value = prop.simple_value();
        var stops = prop.stops();

        function scale(scale_min, scale_max, range_min, range_max, value) {
            var scale_w = scale_max - scale_min;
            var range_w = range_max - range_min;

            return scale_min + (scale_w / range_w) * (value - range_min);
        }

        if (stops) {
            var pct_start = 0;
            var pct_end = 0;
            var clen = cmax - cmin;

            for (var i=0; i<stops.length; i++) {
                var stop_start = stops[i].start;
                var stop_end = stops[i].end;

                pct_end += stops[i].percentage;

                if (value >= stop_start && value <= stop_end) {
                    var pos = scale(pct_start/100 * clen + cmin,
                                    pct_end/100 * clen + cmin,
                                    stop_start,
                                    stop_end,
                                    value);
                    return pos;
                }
                pct_start = pct_end;
            }

            //  Value out of range
            if (value < rmin)
                return 0;
            return pct_end/100 * clen + cmin;
        }

        var method = range.method || 'linear';

        if (method === 'linear') {
            var factor = (value - rmin) / (rmax - rmin);
            var pos = cmin + factor * (cmax - cmin);

            return pos;
        }

        if (method === 'sine') {
            var half = range.midpoint;
            var min = rmin;
            var max = rmax;
            var width = cmax - cmin;
            var low = cmin;
            var high = cmax;

            while ((high-low) > 0.5) {
                var pos = (low + high) / 2;
                var v = sine_position_value(min, max, half, width, pos);

                if (v > value)
                    high = pos;
                else
                    low = pos;
            }

            return pos.toFixed(0);
        }

        throw 'cap.Property.value_position: method ' + method + ' not implemented';
    }


    function sine_position_value(min, max, half, width, pos) {
        var start;

        if (pos > width/2) {
            start = 1 + (pos-width/2) / (width/2);
            return (1 - Math.sin(Math.PI/2 * start)) * (max-half) + half;
        } else {
            start = 1 - pos / (width/2);
            return half - Math.sin(Math.PI/2 * start) * (half-min);
        }
    }

    /*------------------------------------------------------------
     *  Handle: class for a pointer to a variable
     *------------------------------------------------------------*/

    Handle = cap.Handle = function(v) {
        assert_variable(v);

        Literal.call(this);
        this._variable = v;

        return this;
    }

    ist.extend(Handle, Literal);

    Handle.prototype.toString = function() {
        return this.variable().toString();
    }

    Handle.prototype.latex = function() {
        return this.variable().latex();
    }

    Handle.prototype.copy = function() {
        return new Handle(this._variable);
    }

    Handle.prototype.variables = function() {
        return [this.variable()];
    }

    Handle.prototype.equal = function(expr) {
        if (expr instanceof Handle && this.variable() === expr.variable())
            return true;
        if (expr instanceof Variable && this.variable() === expr)
            return true;
        return false;
    }

    Handle.prototype.value = function() {
        return this._variable.value();
    }

    Handle.prototype.evaluate = function() {
        return this._variable.evaluate();
    }

    Handle.prototype.symbol = function() {
        return this._variable.symbol();
    }

    Handle.prototype.variable = function(v) {
        if (v === undefined)
            return this._variable;
        this._variable = v;

        return this;
    }


    /*------------------------------------------------------------
     *  Assert types
     *------------------------------------------------------------*/

    function assert_string(str) {
        if (typeof(str) !== 'string')
            throw 'Error: expected ' + str + ' to be a string';
    }

    function assert_number(num) {
        if (typeof(num) !== 'number')
            throw 'Error: expected ' + num + ' to be a number';
    }

    function assert_optional_number(num) {
        if (num === undefined)
            return;
        if (typeof(num) !== 'number')
            throw 'Error: expected ' + num + ' to be a number';
    }

    function assert_variable(v) {
        if (!(v instanceof Variable))
            throw 'Error: expected ' + v + ' to be a variable';
    }

    function assert_equation(v) {
        if (!(v instanceof Equation))
            throw 'Error: expected ' + v + ' to be an equation';
    }


    /*------------------------------------------------------------
     *  Identity functions
     *------------------------------------------------------------*/

    function is_string(str) {
        return typeof(str) === 'string';
    }

    function is_number(num) {
        return typeof(num) === 'number';
    }

    function is_variable(v) {
        return v instanceof Variable;
    }

    function is_handle(v) {
        return v instanceof Handle;
    }

    function is_expression(v) {
        return v instanceof Expression;
    }

    function init() {
        define_base_prefixes();
    }

    /** Return a new array in which all duplicate elements of the argument array
     *  are removed.
     *
     *  @param {Array} a	Input array.
     *  @returns {Array} 	New array without any duplicate elements.
     */
    cap.remove_duplicates = function(array) {
        var rval = [];

        for (var i=0; i<array.length; i++) {
            var e = array[i];

            if (array.indexOf(e, i+1) === -1)
                rval.push(e);
        }

        return rval;
    };

    init();
}).call(this);


function variable(s) { return new cap.Variable(s); }
function equation(lhs, rhs) { return new cap.Equation(lhs, rhs); }
function add(lhs, rhs) { return new cap.Add(lhs, rhs); }
function subtract(lhs, rhs) { return new cap.Subtract(lhs, rhs); }
function multiply(lhs, rhs) { return new cap.Multiply(lhs, rhs); }
function divide(lhs, rhs) { return new cap.Divide(lhs, rhs); }
function minus(lhs, rhs) { return new cap.Minus(lhs, rhs); }
function any(name) { return new cap.Any(name); }
function any_number(name) { return new cap.AnyNumber(name); }
function negate(expr) { return new cap.Negate(expr); }
function equation(lhs,rhs) { return new cap.Equation(lhs,rhs); }
function number(num) { return new cap.Number(num); }
function power(base, exp) { return new cap.Power(base,exp); }
function property(atts) { return new cap.Property(atts); }
function sqrt(arg) { return new cap.Sqrt(arg); }
function sin(arg) { return new cap.Sine(arg); }
function cos(arg) { return new cap.Cosine(arg); }
function tan(arg) { return new cap.Tangent(arg); }
function asin(arg) { return new cap.ArcSine(arg); }
function acos(arg) { return new cap.ArcCosine(arg); }
function atan(arg) { return new cap.ArcTangent(arg); }
function distance(x1,y1,x2,y2) { return sqrt(add(power(subtract(x1,x2),2),
                                                 power(subtract(y1,y2),2))); }
