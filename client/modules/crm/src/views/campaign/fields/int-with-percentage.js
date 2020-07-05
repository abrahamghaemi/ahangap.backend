

Espo.define('crm:views/campaign/fields/int-with-percentage', 'views/fields/int', function (Dep) {

    return Dep.extend({

        getValueForDisplay: function () {
            var percentageFieldName = this.name.substr(0, this.name.length - 5) + 'Percentage';
            var value = this.model.get(this.name) ;

            var percentageValue = this.model.get(percentageFieldName);
            if (percentageValue !== null && typeof percentageValue !== 'undefined' && percentageValue) {
                value += ' ' + '(' + this.model.get(percentageFieldName) + '%)';
            }
            return value;
        },
    });

});
