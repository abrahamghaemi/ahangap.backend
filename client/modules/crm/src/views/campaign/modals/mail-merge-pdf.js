

Espo.define('crm:views/campaign/modals/mail-merge-pdf', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        template: 'crm:campaign/modals/mail-merge-pdf',

        data: function () {
            return {
                linkList: this.linkList
            };
        },

        setup: function () {
            Dep.prototype.setup.call(this);
            this.headerHtml = this.translate('Generate Mail Merge PDF', 'labels', 'Campaign');
            var linkList = ['contacts', 'leads', 'accounts', 'users'];
            this.linkList = [];
            linkList.forEach(function (link) {
                if (!this.model.get(link + 'TemplateId')) return;
                var targetEntityType = this.getMetadata().get(['entityDefs', 'TargetList', 'links', link, 'entity']);
                if (!this.getAcl().checkScope(targetEntityType)) return;
                this.linkList.push(link);
            }, this);

            this.buttonList.push({
                name: 'proceed',
                label: 'Proceed',
                style: 'danger'
            });

            this.buttonList.push({
                name: 'cancel',
                label: 'Cancel'
            });
        },

        actionProceed: function () {
            var link = this.$el.find('.field[data-name="link"] select').val();
            this.trigger('proceed', link);
        }

    });
});
