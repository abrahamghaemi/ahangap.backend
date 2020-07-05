

Espo.define('crm:views/document/fields/name', 'views/fields/varchar', function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);
            if (this.model.isNew()) {
                this.listenTo(this.model, 'change:fileName', function () {
                    this.model.set('name', this.model.get('fileName'));
                }, this);
            }
        },

    });

});
