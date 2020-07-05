

Espo.define('crm:views/knowledge-base-article/record/detail', 'views/record/detail', function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.getUser().isPortal()) {
                this.sideDisabled = true;
            }

            if (this.getAcl().checkScope('Email', 'create')) {
                this.dropdownItemList.push({
                    'label': 'Send in Email',
                    'name': 'sendInEmail'
                });
            }

            if (this.getUser().isPortal()) {
                if (!this.getAcl().checkScope(this.scope, 'edit')) {
                    if (!this.model.getLinkMultipleIdList('attachments').length) {
                        this.hideField('attachments');
                        this.listenToOnce(this.model, 'sync', function () {
                            if (this.model.getLinkMultipleIdList('attachments').length) {
                                this.showField('attachments');
                            }
                        }, this);
                    }
                }
            }
        },

        actionSendInEmail: function () {
            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));
            Espo.require('crm:knowledge-base-helper', function (Helper) {
                var helper = new Helper(this.getLanguage());

                helper.getAttributesForEmail(this.model, {}, function (attributes) {
                    var viewName = this.getMetadata().get('clientDefs.Email.modalViews.compose') || 'views/modals/compose-email';
                    this.createView('composeEmail', viewName, {
                        attributes: attributes,
                        selectTemplateDisabled: true,
                        signatureDisabled: true
                    }, function (view) {
                        Espo.Ui.notify(false);
                        view.render();
                    }, this);
                }.bind(this));
            }, this);
        },

    });
});

