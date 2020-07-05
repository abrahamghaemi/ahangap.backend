

Espo.define('views/fields/enum-float', 'views/fields/enum-int', function (Dep) {

    return Dep.extend({

        type: 'enumFloat',

        fetch: function () {
            var value = parseFloat(this.$element.val());
            var data = {};
            data[this.name] = value;
            return data;
        },

        parseItemForSearch: function (item) {
            return parseFloat(item);
        }
    });
});

