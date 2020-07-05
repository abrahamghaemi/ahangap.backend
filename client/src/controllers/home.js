

define('controllers/home', 'controller', function (Dep) {

    return Dep.extend({

        actionIndex: function () {
            this.main('views/home', null);
        }
    });
});
