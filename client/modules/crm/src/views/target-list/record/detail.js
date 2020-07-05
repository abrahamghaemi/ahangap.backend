

Espo.define('crm:views/target-list/record/detail', 'views/record/detail', function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);
            this.listenTo(this.model, 'after:relate', function () {
                this.model.fetch();
            }, this);

            this.listenTo(this.model, 'after:unrelate', function () {
                this.model.fetch();
            }, this);
        }

    });
});

