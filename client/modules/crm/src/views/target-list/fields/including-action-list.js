

Espo.define('crm:views/target-list/fields/including-action-list', 'views/fields/multi-enum', function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = this.getMetadata().get('entityDefs.CampaignLogRecord.fields.action.options') || [];
            this.translatedOptions = {};
            this.params.options.forEach(function (item) {
                this.translatedOptions[item] = this.getLanguage().translateOption(item, 'action', 'CampaignLogRecord');
            }, this);
        }
    });
});

