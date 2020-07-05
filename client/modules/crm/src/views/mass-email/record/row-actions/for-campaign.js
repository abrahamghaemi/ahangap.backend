

Espo.define('crm:views/mass-email/record/row-actions/for-campaign', 'views/record/row-actions/relationship-no-unlink', function (Dep) {

    return Dep.extend({

        getActionList: function () {
            var actionList = Dep.prototype.getActionList.call(this);

            if (this.options.acl.edit && !~['Complete'].indexOf(this.model.get('status'))) {
                actionList.unshift({
                    action: 'sendTest',
                    label: 'Send Test',
                    data: {
                        id: this.model.id
                    }
                });
            }

            return actionList;
        }
    });

});
