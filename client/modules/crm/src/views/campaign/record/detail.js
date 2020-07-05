


Espo.define('crm:views/campaign/record/detail', 'views/record/detail', function (Dep) {

    return Dep.extend({

        duplicateAction: true,

        bottomView: 'crm:views/campaign/record/detail-bottom',

        setupActionItems: function () {
            Dep.prototype.setupActionItems.call(this);
            this.dropdownItemList.push({
                'label': 'Generate Mail Merge PDF',
                'name': 'generateMailMergePdf',
                'hidden': !this.isMailMergeAvailable()
            });

            this.listenTo(this.model, 'change', function () {
                if (this.isMailMergeAvailable()) {
                    this.showActionItem('generateMailMergePdf');
                } else {
                    this.hideActionItem('generateMailMergePdf');
                }
            }, this);
        },

        afterRender: function () {
        	Dep.prototype.afterRender.call(this);
        },

        isMailMergeAvailable: function () {
            if (this.model.get('type') !== 'Mail') return false;

            if (!this.model.get('targetListsIds') || !this.model.get('targetListsIds').length) return false;

            if (
                !this.model.get('leadsTemplateId')
                &&
                !this.model.get('contactsTemplateId')
                &&
                !this.model.get('accountsTemplateId')
                &&
                !this.model.get('usersTemplateId')
            ) return false;

            return true;
        },

        actionGenerateMailMergePdf: function () {
            this.createView('dialog', 'crm:views/campaign/modals/mail-merge-pdf', {
                model: this.model
            }, function (view) {
                view.render();
                this.listenToOnce(view, 'proceed', function (link) {
                    this.clearView('dialog');
                    this.ajaxPostRequest('Campaign/action/generateMailMergePdf', {
                        campaignId: this.model.id,
                        link: link
                    }).then(function (response) {
                        window.open('?entryPoint=download&id=' + response.id, '_blank');
                    }.bind(this));
                }, this);
            });
        }

    });
});
