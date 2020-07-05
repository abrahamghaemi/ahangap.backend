

define('views/deleted-detail', 'views/detail', function (Dep) {

    return Dep.extend({

        recordView: 'views/record/deleted-detail',

        menuDisabled: true,

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.model.get('deleted')) {
                this.menuDisabled = true;
            }
        },

        getRecordViewName: function () {
            return this.recordView;
        },

    });
});
