

define('views/record/row-actions/empty', 'views/record/row-actions/default', function (Dep) {

    return Dep.extend({

        getActionList: function () {
            return [];
        }

    });
});
