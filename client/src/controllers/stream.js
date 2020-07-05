

define('controllers/stream', 'controller', function (Dep) {

    return Dep.extend({

        defaultAction: 'index',

        actionIndex: function () {
            this.main('views/stream', {
                displayTitle: true,
            }, function (view) {
                view.render();
            });
        },

        actionPosts: function () {
            this.main('views/stream', {
                displayTitle: true,
                filter: 'posts',
            }, function (view) {
                view.render();
            });
        },

        actionUpdates: function () {
            this.main('views/stream', {
                displayTitle: true,
                filter: 'updates',
            }, function (view) {
                view.render();
            });
        },

    });
});
