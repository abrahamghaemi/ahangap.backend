

define('crm:views/meeting/modals/detail', 'views/modals/detail', function (Dep) {

    return Dep.extend({

        setupAfterModelCreated: function () {
            Dep.prototype.setupAfterModelCreated.call(this);

            var buttonData = this.getAcceptanceButtonData();

            this.addButton({
                name: 'setAcceptanceStatus',
                html: buttonData.html,
                hidden: this.hasAcceptanceStatusButton(),
                style: buttonData.style,
            }, 'cancel');

            if (
                !~this.getAcl().getScopeForbiddenFieldList(this.model.entityType).indexOf('status')
            ) {
                this.addDropdownItem({
                    name: 'setHeld',
                    html: this.translate('Set Held', 'labels', this.model.entityType),
                    hidden: true,
                });
                this.addDropdownItem({
                    name: 'setNotHeld',
                    html: this.translate('Set Not Held', 'labels', this.model.entityType),
                    hidden: true,
                });
            }

            this.initAcceptenceStatus();
            this.on('switch-model', function (model, previousModel) {
                this.stopListening(previousModel, 'sync');
                this.initAcceptenceStatus();
            }, this);

             this.on('after:save', function () {
                if (this.hasAcceptanceStatusButton()) {
                    this.showAcceptanceButton();
                } else {
                    this.hideAcceptanceButton();
                }
            }, this);
        },

        controlRecordButtonsVisibility: function () {
            Dep.prototype.controlRecordButtonsVisibility.call(this);
            this.controlStatusActionVisibility();
        },

        controlStatusActionVisibility: function () {
            if (this.getAcl().check(this.model, 'edit') && !~['Held', 'Not Held'].indexOf(this.model.get('status'))) {
                this.showActionItem('setHeld');
                this.showActionItem('setNotHeld');
            } else {
                this.hideActionItem('setHeld');
                this.hideActionItem('setNotHeld');
            }
        },

        hasSetStatusButton: function () {

        },

        initAcceptenceStatus: function () {
            if (this.hasAcceptanceStatusButton()) {
                this.showAcceptanceButton();
            } else {
                this.hideAcceptanceButton();
            }

            this.listenTo(this.model, 'sync', function () {
                if (this.hasAcceptanceStatusButton()) {
                    this.showAcceptanceButton();
                } else {
                    this.hideAcceptanceButton();
                }
            }, this);
        },

        getAcceptanceButtonData: function () {
            var acceptanceStatus = this.model.getLinkMultipleColumn('users', 'status', this.getUser().id);

            var html;
            var style = 'default';
            if (acceptanceStatus && acceptanceStatus !== 'None') {
                html = this.getLanguage().translateOption(acceptanceStatus, 'acceptanceStatus', this.model.entityType);
                style = this.getMetadata().get(['entityDefs', this.model.entityType, 'fields', 'acceptanceStatus', 'style', acceptanceStatus]);
            } else {
                html = this.translate('Acceptance', 'labels', 'Meeting');
            }

            return {
                style: style,
                html: html
            };
        },

        showAcceptanceButton: function () {
            this.showButton('setAcceptanceStatus');

            if (!this.isRendered()) {
                this.once('after:render', this.showAcceptanceButton, this);
                return;
            }

            var data = this.getAcceptanceButtonData();

            var $button = this.$el.find('.modal-footer [data-name="setAcceptanceStatus"]');

            $button.html(data.html);

            $button.removeClass('btn-default');
            $button.removeClass('btn-success');
            $button.removeClass('btn-warning');
            $button.removeClass('btn-info');
            $button.removeClass('btn-primary');
            $button.removeClass('btn-danger');
            $button.addClass('btn-' + data.style);
        },

        hideAcceptanceButton: function () {
            this.hideButton('setAcceptanceStatus');
        },

        hasAcceptanceStatusButton: function () {
            if (!this.model.has('status')) return;
            if (!this.model.has('usersIds')) return;

            if (~['Held', 'Not Held'].indexOf(this.model.get('status'))) {
                return;
            }

            if (!~this.model.getLinkMultipleIdList('users').indexOf(this.getUser().id)) {
                return;
            }

            var acceptanceStatus = this.model.getLinkMultipleColumn('users', 'status', this.getUser().id);

            var html;
            var style = 'default';
            if (acceptanceStatus && acceptanceStatus !== 'None') {
                html = this.getLanguage().translateOption(acceptanceStatus, 'acceptanceStatus', this.model.entityType);
                style = this.getMetadata().get(['entityDefs', this.model.entityType, 'fields', 'acceptanceStatus', 'style', acceptanceStatus]);
            } else {
                html = this.translate('Acceptance', 'labels', 'Meeting');
            }

            return true;
        },

        actionSetAcceptanceStatus: function () {
            var acceptanceStatus = this.model.getLinkMultipleColumn('users', 'status', this.getUser().id);

            this.createView('dialog', 'crm:views/meeting/modals/acceptance-status', {
                model: this.model
            }, function (view) {
                view.render();

                this.listenTo(view, 'set-status', function (status) {
                    this.hideAcceptanceButton();
                    Espo.Ajax.postRequest(this.model.entityType + '/action/setAcceptanceStatus', {
                        id: this.model.id,
                        status: status
                    }).then(function () {
                        this.model.fetch();
                    }.bind(this));
                });
            });
        },

        actionSetHeld: function () {
            this.model.save({status: 'Held'});
            this.trigger('after:save');
        },

        actionSetNotHeld: function () {
            this.model.save({status: 'Not Held'});
            this.trigger('after:save');
        },
    });
});
