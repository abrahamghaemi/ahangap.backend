

Espo.define('crm:views/opportunity/record/edit', 'views/record/edit', function (Dep) {

    return Dep.extend({

        populateDefaults: function () {
            Dep.prototype.populateDefaults.call(this);

            var probabilityMap = this.getMetadata().get('entityDefs.Opportunity.fields.stage.probabilityMap') || {};

            var stage = this.model.get('stage');
            if (stage in probabilityMap) {
                this.model.set('probability', probabilityMap[stage], {silent: true});
            }
        }
    });
});

