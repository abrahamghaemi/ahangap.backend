

define('controllers/notification', 'controller', function (Dep) {

    return Dep.extend({

        defaultAction: 'index',

        actionIndex: function () {
            this.main('views/notification/list', {
            }, function (view) {
                view.render();
            });
        }

    });
});
