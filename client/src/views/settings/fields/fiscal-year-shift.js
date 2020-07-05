

Espo.define('views/settings/fields/fiscal-year-shift', 'views/fields/enum-int', function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            this.params.options = [];
            this.translatedOptions = {};
            var monthNameList = this.getLanguage().get('Global', 'lists', 'monthNames') || [];
            monthNameList.forEach(function (name, i) {
                this.params.options.push(i);
                this.translatedOptions[i] = name;
            }, this);
        }
    });
});
