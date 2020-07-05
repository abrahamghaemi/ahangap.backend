

define('views/record/deleted-detail', ['views/record/detail'], function (Dep) {

    return Dep.extend({

        bottomView: null,

        sideView: 'views/record/deleted-detail-side',

        setupBeforeFinal: function () {
            Dep.prototype.setupBeforeFinal.call(this);

            this.buttonList = [];
            this.dropdownItemList = [];

            this.addDropdownItem({
                name: 'restoreDeleted',
                label: 'Restore'
            });
        },

        actionRestoreDeleted: function () {
            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));
            Espo.Ajax.postRequest(this.model.entityType + '/action/restoreDeleted', {
                id: this.model.id
            }).then(function () {
                Espo.Ui.notify(false);
                this.model.set('deleted', false);
                this.model.trigger('after:restore-deleted');
            }.bind(this));
        },

    });
});
