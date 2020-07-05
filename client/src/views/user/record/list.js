

Espo.define('views/user/record/list', 'views/record/list', function (Dep) {

    return Dep.extend({

        quickEditDisabled: true,

        rowActionsView: 'views/user/record/row-actions/default',

        massActionList: ['remove', 'massUpdate', 'export'],

        checkAllResultMassActionList: ['massUpdate', 'export'],

        setupMassActionItems: function () {
            Dep.prototype.setupMassActionItems.call(this);

            if (this.scope === 'ApiUser') {
                this.removeMassAction('massUpdate');
                this.removeMassAction('export');

                this.layoutName = 'listApi';
            }
            if (this.scope === 'PortalUser') {
                this.layoutName = 'listPortal';
            }
            if (!this.getUser().isAdmin()) {
                this.removeMassAction('massUpdate');
                this.removeMassAction('export');
            }
        },

        getModelScope: function (id) {
            var model = this.collection.get(id);

            if (model.isPortal()) {
                return 'PortalUser';
            }
            return this.scope;
        }
    });

});

