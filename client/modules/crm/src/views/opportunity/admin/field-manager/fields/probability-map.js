

Espo.define('crm:views/opportunity/admin/field-manager/fields/probability-map', 'views/fields/base', function (Dep) {

    return Dep.extend({

        editTemplate: 'crm:opportunity/admin/field-manager/fields/probability-map/edit',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.listenTo(this.model, 'change:options', function () {
                this.reRender();
            }, this);
        },

        data: function () {
            var data = {};
            var values = this.model.get('probabilityMap') || {};
            data.stageList = this.model.get('options') || [];
            data.values = values;
            return data;
        },

        fetch: function () {
            var data = {
                probabilityMap: {}
            };

            (this.model.get('options') || []).forEach(function (item) {
                data.probabilityMap[item] = parseInt(this.$el.find('input[data-name="'+item+'"]').val());

            }, this);

            return data;
        },

        afterRender: function () {
            this.$el.find('input').on('change', function () {
                this.trigger('change')
            }.bind(this));
        }

    });

});
