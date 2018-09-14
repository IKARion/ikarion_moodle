define(['jquery', 'core/ajax', 'core/modal_factory', 'core/modal_events', 'core/templates', 'core/str', 'core/yui'], function ($, ajax, ModalFactory, ModalEvents, Templates, str, Y) {

    /**
     * Constructor
     *
     * @param {String} Policy text.
     *
     * Each call to init gets it's own instance of this class.
     */
    var Choice = function (policytext, show, selected) {
        this.policytext = policytext;
        this.show = show;
        this.selected = selected;
        this.init();
    };

    /**
     * @var {Modal} modal
     * @private
     */
    Choice.prototype.modal = null;

    /**
     * @var {String} policytext
     * @private
     */
    Choice.prototype.policytext = '';

    /**
     * @var {String} show modal
     * @private
     */
    Choice.prototype.show = 0;

    /**
     * @var {String} preset choice
     * @private
     */
    Choice.prototype.choice = null;

    /**
     * Initialise the class.
     *
     * @param {String} Policy text.
     * @private
     * @return {Promise}
     */
    Choice.prototype.init = function () {
        var trigger = $('#open-policy-modal');

        // Fetch the title string.
        return str.get_string('policy-text', 'block_ikarionconsent').then(function (title) {
            // Create the modal.
            return ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: title,
                body: this.getBody()
            }, trigger);
        }.bind(this)).then(function (modal) {
            // Keep a reference to the modal.
            this.modal = modal;

            // Forms are big, we want a big modal.
            this.modal.setLarge();

            // We want to reset the form every time it is opened.
            this.modal.getRoot().on(ModalEvents.hidden, function () {
                this.modal.setBody(this.getBody());
            }.bind(this));

            // We want to hide the submit buttons every time it is opened.
            this.modal.getRoot().on(ModalEvents.shown, function () {
                this.modal.getRoot().append('<style>[data-action=save] {display: none}</style>');
            }.bind(this));

            this.modal.getRoot().on('change', function () {
                this.modal.getRoot().append('<style>[data-action=save] {display: block!important}</style>');
            }.bind(this));

            // We catch the modal save event, and use it to submit the form inside the modal.
            // Triggering a form submission will give JS validation scripts a chance to check for errors.
            this.modal.getRoot().on(ModalEvents.save, this.submitForm.bind(this));
            // We also catch the form submit event and use it to submit the form with ajax.
            this.modal.getRoot().on('submit', 'form', this.submitFormAjax.bind(this));

            if (this.show == 1) {
                this.modal.show();
            }

            return this.modal;
        }.bind(this));
    };

    /**
     * @method getBody
     * @private
     * @return {Promise}
     */
    Choice.prototype.getBody = function () {
        return Templates.render('block_ikarionconsent/modalbody', {policy: this.policytext, selected: this.selected});
    };

    /**
     * @method handleFormSubmissionResponse
     * @private
     * @return {Promise}
     */
    Choice.prototype.handleFormSubmissionResponse = function () {
        this.modal.hide();
        // We could trigger an event instead.
        // Yuk.
        Y.use('moodle-core-formchangechecker', function () {
            M.core_formchangechecker.reset_form_dirty_state();
        });
        document.location.reload();
    };

    /**
     * @method handleFormSubmissionFailure
     * @private
     * @return {Promise}
     */
    Choice.prototype.handleFormSubmissionFailure = function (data) {
        this.modal.setBody(data);
    };

    /**
     * Private method
     *
     * @method submitFormAjax
     * @private
     * @param {Event} e Form submission event.
     */
    Choice.prototype.submitFormAjax = function (e) {
        // We don't want to do a real form submission.
        e.preventDefault();

        // Convert all the form elements values to a serialised string.
        var formData = this.modal.getRoot().find('form').serialize();
        var val = formData.split("=");
        var val = val[1];

        ajax.call([{
            methodname: 'block_ikarionconsent_set_choice',
            args: {choice: val},
            done: this.handleFormSubmissionResponse.bind(this, formData),
            fail: this.handleFormSubmissionFailure.bind(this, formData)
        }]);
    };

    /**
     * @method handleFormSubmissionResponse
     * @private
     * @return {Promise}
     */
    Choice.prototype.handleFormSubmissionResponse = function () {
        this.modal.hide();
        // We could trigger an event instead.
        // Yuk.
        Y.use('moodle-core-formchangechecker', function () {
            M.core_formchangechecker.reset_form_dirty_state();
        });
        document.location.reload();
    };

    /**
     * @method handleFormSubmissionFailure
     * @private
     * @return {Promise}
     */
    Choice.prototype.handleFormSubmissionFailure = function (data) {
        // Oh noes! Epic fail :(
        // Ah wait - this is normal. We need to re-display the form with errors!
        this.modal.setBody(this.getBody());
    };

    /**
     * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
     *
     * @method submitForm
     * @param {Event} e Form submission event.
     * @private
     */
    Choice.prototype.submitForm = function (e) {
        e.preventDefault();
        this.modal.getRoot().find('form').submit();
    };

    return {
        init: function (policytext, show, selected) {
            return new Choice(policytext, show, selected);
        }
    };
});