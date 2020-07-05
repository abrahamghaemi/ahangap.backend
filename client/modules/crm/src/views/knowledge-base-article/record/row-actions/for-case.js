

Espo.define('crm:views/knowledge-base-article/record/row-actions/for-case', 'views/record/row-actions/relationship-view-and-unlink', function (Dep) {

    return Dep.extend({

        getActionList: function () {
            var actionList = Dep.prototype.getActionList.call(this);

            if (this.getAcl().checkScope('Email', 'create')) {
                actionList.push({
                    action: 'sendInEmail',
                    label: 'Send in Email',
                    data: {
                        id: this.model.id
                    }
                });
            }

            return actionList;
        }
    });

});
