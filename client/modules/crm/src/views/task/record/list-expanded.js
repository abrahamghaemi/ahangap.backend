

Espo.define('crm:views/task/record/list-expanded', ['views/record/list-expanded', 'crm:views/task/record/list'], function (Dep, List) {

    return Dep.extend({

        rowActionsView: 'crm:views/task/record/row-actions/default',

        actionSetCompleted: function (data) {
            List.prototype.actionSetCompleted.call(this, data);
        },

    });

});
