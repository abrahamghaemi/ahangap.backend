

Espo.define('views/login', 'view', function (Dep) {

    return Dep.extend({

        template: 'login',

        views: {
            footer: {
                el: 'body > footer',
                view: 'views/site/footer'
            },
        },

        events: {
            'submit #login-form': function (e) {
                this.login();
                return false;
            },
            'click a[data-action="passwordChangeRequest"]': function (e) {
                this.showPasswordChangeRequest();
            }
        },

        data: function () {
            return {
                logoSrc: this.getLogoSrc()
            };
        },

        getLogoSrc: function () {
            var companyLogoId = this.getConfig().get('companyLogoId');
            if (!companyLogoId) {
                return this.getBasePath() + ('client/img/logo.png');
            }
            return this.getBasePath() + '?entryPoint=LogoImage&id='+companyLogoId;
        },

        login: function () {
                var userName = $('#field-userName').val();
                var trimmedUserName = userName.trim();
                if (trimmedUserName !== userName) {
                    $('#field-userName').val(trimmedUserName);
                    userName = trimmedUserName;
                }

                var password = $('#field-password').val();

                var $submit = this.$el.find('#btn-login');

                if (userName == '') {

                    this.isPopoverDestroyed = false;
                    var $el = $("#field-userName");

                    var message = this.getLanguage().translate('userCantBeEmpty', 'messages', 'User');

                    $el.popover({
                        placement: 'bottom',
                        container: 'body',
                        content: message,
                        trigger: 'manual',
                    }).popover('show');

                    var $cell = $el.closest('.form-group');
                    $cell.addClass('has-error');
                    $el.one('mousedown click', function () {
                        $cell.removeClass('has-error');
                        if (this.isPopoverDestroyed) return;
                        $el.popover('destroy');
                        this.isPopoverDestroyed = true;
                    }.bind(this));
                    return;
                }

                $submit.addClass('disabled').attr('disabled', 'disabled');

                this.notify('Please wait...');

                $.ajax({
                    url: 'App/user',
                    headers: {
                        'Authorization': 'Basic ' + Base64.encode(userName  + ':' + password),
                        'App-Authorization': Base64.encode(userName + ':' + password),
                        'App-Authorization-By-Token': false
                    },
                    success: function (data) {
                        this.notify(false);
                        this.trigger('login', {
                            auth: {
                                userName: userName,
                                token: data.token
                            },
                            user: data.user,
                            preferences: data.preferences,
                            acl: data.acl,
                            settings: data.settings,
                            appParams: data.appParams
                        });

                        // snapp
                        // add check language and add to local storage
                        var language = data.preferences.language;
                        var isRtl = Espo.Utils.setRtlStatus(language);
                        if (isRtl) {
                           // Espo.Utils.addMetaLink()
                        }
                        // end snapp
                    }.bind(this),
                    error: function (xhr) {
                        $submit.removeClass('disabled').removeAttr('disabled');
                        if (xhr.status == 401) {
                            this.onWrong();
                        }
                    }.bind(this),
                    login: true,
                });
        },

        onWrong: function () {
            var cell = $('#login .form-group');
            cell.addClass('has-error');
            this.$el.one('mousedown click', function () {
                cell.removeClass('has-error');
            });
            Espo.Ui.error(this.translate('wrongUsernamePasword', 'messages', 'User'));
        },

        showPasswordChangeRequest: function () {
            this.notify('Please wait...');
            this.createView('passwordChangeRequest', 'views/modals/password-change-request', {
                url: window.location.href
            }, function (view) {
                view.render();
                view.notify(false);
            });
        }
    });

});
