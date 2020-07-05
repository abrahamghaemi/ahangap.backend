

Espo.define('views/extension/record/list', 'views/record/list', function (Dep) {

    return Dep.extend({

        rowActionsView: 'views/extension/record/row-actions',

        checkboxes: false,

    	quickDetailDisabled: true,

        quickEditDisabled: true,

        massActionList: []

    });

});

