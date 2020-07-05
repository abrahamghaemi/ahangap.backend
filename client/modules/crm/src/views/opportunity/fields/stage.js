

Espo.define('crm:views/opportunity/fields/stage', 'views/fields/enum', function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.probabilityMap = this.getMetadata().get('entityDefs.Opportunity.fields.stage.probabilityMap') || {};

            if (this.mode != 'list') {
                this.on('change', function () {
                    this.model.set('probability', this.probabilityMap[this.model.get(this.name)]);
                }, this);
            }
        }
    });
});
