

Espo.define('crm:views/mass-email/record/edit', 'views/record/edit', function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);
            this.initFieldsControl();
        },

        initFieldsControl: function () {
            this.listenTo(this.model, 'change:smtpAccount', function (model, value, o) {
                if (!o.ui) return;

                if (!value || value === 'system') {
                    this.model.set('fromAddress', this.getConfig().get('outboundEmailFromAddress') || '');
                    this.model.set('fromName', this.getConfig().get('outboundEmailFromName') || '');
                    return;
                }
                var smtpAccountView = this.getFieldView('smtpAccount');
                if (!smtpAccountView) return;
                if (!smtpAccountView.loadedOptionAddresses) return;
                if (!smtpAccountView.loadedOptionAddresses[value]) return;
                this.model.set('fromAddress', smtpAccountView.loadedOptionAddresses[value]);
                this.model.set('fromName', smtpAccountView.loadedOptionFromNames[value]);
            }, this);
        }

    });
});
