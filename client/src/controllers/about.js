

define('controllers/about', 'controller', function (Dep) {

    return Dep.extend({

        defaultAction: 'about',

        actionAbout: function () {
            this.main('About', {}, function (view) {
                view.render();
            });
        }
    });

});
