

Espo.define('crm:views/target-list/fields/target-status', 'views/fields/base', function (Dep) {

    return Dep.extend({

        getValueForDisplay: function () {
            if (this.model.get('isOptedOut')) {
                return this.getLanguage().translateOption('Opted Out', 'targetStatus', 'TargetList');
            }

            return this.getLanguage().translateOption('Listed', 'targetStatus', 'TargetList');
        }
    });
});
