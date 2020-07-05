

define('controllers/dashboard', 'controller', function (Dep) {

    return Dep.extend({

        defaultAction: 'index',

        actionIndex: function () {
            this.main('views/dashboard', {
                displayTitle: true,
            }, function (view) {
                view.render();
            });
        }

    });

});
