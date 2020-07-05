

Espo.define('views/modals/password-change-request', 'views/modal', function (Dep) {

    return Dep.extend({

        cssName: 'password-change-request',

        template: 'modals/password-change-request',

        setup: function () {

            this.buttonList = [
                {
                    name: 'submit',
                    label: 'Submit',
                    style: 'danger'
                },
                {
                    name: 'cancel',
                    label: 'Close'
                }
            ];

            this.headerHtml = this.translate('Password Change Request', 'labels', 'User');
        },

        actionSubmit: function () {
            var $userName = this.$el.find('input[name="username"]');
            var $emailAddress = this.$el.find('input[name="emailAddress"]');

            var userName = $userName.val();
            var emailAddress = $emailAddress.val();


            var isValid = true;

            if (userName == '') {
                isValid = false;

                var message = this.getLanguage().translate('userCantBeEmpty', 'messages', 'User');

                this.isPopoverUserNameDestroyed = false;

                $userName.popover({
                    container: 'body',
                    placement: 'bottom',
                    content: message,
                    trigger: 'manual',
                }).popover('show');

                var $cellUserName = $userName.closest('.form-group');
                $cellUserName.addClass('has-error');

                $userName.one('mousedown click', function () {
                    $cellUserName.removeClass('has-error');
                    if (this.isPopoverUserNameDestroyed) return;
                    $userName.popover('destroy');
                    this.isPopoverUserNameDestroyed = true;
                }.bind(this));
            }

            if (emailAddress == '') {
                isValid = false;

                var message = this.getLanguage().translate('emailAddressCantBeEmpty', 'messages', 'User');

                this.isPopoverEmailAddressDestroyed = false;

                $emailAddress.popover({
                    container: 'body',
                    placement: 'bottom',
                    content: message,
                    trigger: 'manual',
                }).popover('show');

                var $cellEmailAddress = $emailAddress.closest('.form-group');
                $cellEmailAddress.addClass('has-error');

                $emailAddress.one('mousedown click', function () {
                    $cellEmailAddress.removeClass('has-error');
                    if (this.isPopoverEmailAddressDestroyed) return;
                    $emailAddress.popover('destroy');
                    this.isPopoverEmailAddressDestroyed = true;
                }.bind(this));
            }

            if (!isValid) return;

            $submit = this.$el.find('button[data-name="submit"]');
            $submit.addClass('disabled');

            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

            Espo.Ajax.postRequest('User/passwordChangeRequest', {
                userName: userName,
                emailAddress: emailAddress,
                url: this.options.url,
            }).then(function () {
                Espo.Ui.notify(false);

                var msg = this.translate('uniqueLinkHasBeenSent', 'messages', 'User');

                this.$el.find('.cell-userName').addClass('hidden');
                this.$el.find('.cell-emailAddress').addClass('hidden');

                $submit.addClass('hidden');

                this.$el.find('.msg-box').removeClass('hidden');

                this.$el.find('.msg-box').html('<span class="text-success">' + msg + '</span>');
            }.bind(this)).fail(function (xhr) {
                if (xhr.status == 404) {
                    this.notify(this.translate('userNameEmailAddressNotFound', 'messages', 'User'), 'error');
                    xhr.errorIsHandled = true;
                }
                if (xhr.status == 403) {
                    var statusReasonHeader = xhr.getResponseHeader('X-Status-Reason');
                    if (statusReasonHeader) {
                        try {
                            var response = JSON.parse(statusReasonHeader);
                            if (response.reason === 'Already-Sent') {
                                xhr.errorIsHandled = true;
                                Espo.Ui.error(this.translate('forbidden', 'messages', 'User'), 'error');
                            }
                        } catch (e) {}
                    }
                }
                $submit.removeClass('disabled');
            }.bind(this));
        }

    });
});
