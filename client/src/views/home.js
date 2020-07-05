

define('views/home', 'view', function (Dep) {

    return Dep.extend({

        template: 'home',

        setup: function () {
            var view = this.getMetadata().get(['clientDefs', 'Home', 'view']) || 'views/dashboard';
            this.createView('content', view, {
                el: this.options.el + ' > .home-content'
            });
        }
    });
});
