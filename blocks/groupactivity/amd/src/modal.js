define(['jquery', 'core/ajax', 'core/modal_factory', 'core/modal_events', 'core/templates', 'core/str'],
    function ($, ajax, ModalFactory, ModalEvents, Templates, str) {

        /**
         * Constructor
         *
         * Each call to init gets it's own instance of this class.
         */
        var SelfAssess = function (instance, show) {
            this.instance = instance;
            this.show = show;
            this.init();
        };

        /**
         * @var {Modal} modal
         * @private
         */
        SelfAssess.prototype.modal = null;

        /**
         * @var {Items} items
         * @private
         */
        SelfAssess.prototype.items = null;

        /**
         * @var {String} show modal
         * @private
         */
        SelfAssess.prototype.show = false;

        /**
         * @var {String} instance modal
         * @private
         */
        SelfAssess.prototype.instance = 0;

        /**
         * Initialise the class.
         *
         * @private
         * @return {Promise}
         */
        SelfAssess.prototype.init = function () {
            var trigger = $('#open-self-assess-modal');

            // Fetch the title string.
            return str.get_string('self-assess-title', 'block_groupactivity').then(function (title) {
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
                    this.modal.getRoot().append('<style>[data-action=save] {display: none!important;}</style>');
                }.bind(this));

                this.modal.getRoot().on('change', function () {
                    var check = true;
                    $("input:radio").each(function () {
                        var name = $(this).attr("name");
                        if ($("input:radio[name=" + name + "]:checked").length == 0) {
                            check = false;
                        }
                    });

                    if (check) {
                        this.modal.getRoot().append('<style>[data-action=save] {display: block!important}</style>');
                    }
                }.bind(this));

                // We catch the modal save event, and use it to submit the form inside the modal.
                // Triggering a form submission will give JS validation scripts a chance to check for errors.
                this.modal.getRoot().on(ModalEvents.save, this.submitForm.bind(this));
                // We also catch the form submit event and use it to submit the form with ajax.
                this.modal.getRoot().on('submit', 'form', this.submitFormAjax.bind(this));

                if (this.show == true) {
                    this.modal.show();
                }

                return this.modal;
            }.bind(this));
        };

        SelfAssess.prototype.getItems = function () {
            var items;
            var promises = ajax.call([
                {methodname: 'block_groupactivity_get_selfassess_items', args: {instance: this.instance}}
            ], false);

            promises[0].done(function (response) {
                items = response;
            }).fail(function () {
                items = false;
            });

            return items;
        };

        /**
         * @method getBody
         * @private
         * @return {Promise}
         */
        SelfAssess.prototype.getBody = function () {
            return Templates.render('block_groupactivity/modalbody', {items: this.getItems(), instance: this.instance});
        };

        /**
         * @method handleFormSubmissionResponse
         * @private
         * @return {Promise}
         */
        SelfAssess.prototype.handleFormSubmissionResponse = function () {
            this.modal.hide();
            document.location.reload();
        };

        /**
         * @method handleFormSubmissionFailure
         * @private
         * @return {Promise}
         */
        SelfAssess.prototype.handleFormSubmissionFailure = function (data) {
            this.modal.setBody(data);
        };

        /**
         * Private method
         *
         * @method submitFormAjax
         * @private
         * @param {Event} e Form submission event.
         */
        SelfAssess.prototype.submitFormAjax = function (e) {
            // We don't want to do a real form submission.
            e.preventDefault();

            // Convert all the form elements values to a serialised string.
            var formData = this.modal.getRoot().find('form').serialize();

            ajax.call([{
                methodname: 'block_groupactivity_set_self_assess',
                args: {items: formData},
                done: this.handleFormSubmissionResponse.bind(this, formData),
                fail: this.handleFormSubmissionFailure.bind(this, formData)
            }]);
        };

        /**
         * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
         *
         * @method submitForm
         * @param {Event} e Form submission event.
         * @private
         */
        SelfAssess.prototype.submitForm = function (e) {
            e.preventDefault();
            this.modal.getRoot().find('form').submit();
        };

        return {
            init: function (instance, show) {
                return new SelfAssess(instance, show);
            }
        };
    });
