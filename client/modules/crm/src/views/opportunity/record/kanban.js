

Espo.define('crm:views/opportunity/record/kanban', 'views/record/kanban', function (Dep) {

    return Dep.extend({

        handleAttributesOnGroupChange: function (model, attributes, group) {
            if (this.statusField !== 'stage') return;
            var probability = this.getMetadata().get(['entityDefs', 'Opportunity', 'fields', 'stage', 'probabilityMap', group]);
            probability = parseInt(probability);
            attributes['probability'] = probability;
        }

    });
});
