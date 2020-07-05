

Espo.define('views/admin/field-manager/list', 'view', function (Dep) {

    return Dep.extend({

        template: 'admin/field-manager/list',

        data: function () {
            return {
                scope: this.scope,
                fieldDefsArray: this.fieldDefsArray,
                typeList: this.typeList
            };
        },

        events: {
            'click [data-action="removeField"]': function (e) {
                var field = $(e.currentTarget).data('name');

                this.confirm(this.translate('confirmation', 'messages'), function () {
                    this.notify('Removing...');
                    $.ajax({
                        url: 'Admin/fieldManager/' + this.scope + '/' + field,
                        type: 'DELETE',
                        success: function () {
                            this.notify('Removed', 'success');
                            var data = this.getMetadata().data;
                            delete data['entityDefs'][this.scope]['fields'][field];
                            this.getMetadata().storeToCache();
                            $(e.currentTarget).closest('tr').remove();
                        }.bind(this),
                    });
                }, this);
            }
        },

        setup: function () {
            this.scope = this.options.scope;

            this.typeList = [];

            var fieldDefs = this.getMetadata().get('fields');

            Object.keys(this.getMetadata().get('fields')).forEach(function (type) {
                if (type in fieldDefs) {
                    if (!fieldDefs[type].notCreatable) {
                        this.typeList.push(type);
                    }
                }
            }, this);

            this.typeList.sort(function (v1, v2) {
                return this.translate(v1, 'fieldTypes', 'Admin').localeCompare(this.translate(v2, 'fieldTypes', 'Admin'));
            }.bind(this));

            this.wait(true);
            this.getModelFactory().create(this.scope, function (model) {

                this.fields = model.defs.fields;
                this.fieldList = Object.keys(this.fields).sort();
                this.fieldDefsArray = [];
                this.fieldList.forEach(function (field) {
                    var defs = this.fields[field];
                    if (defs.customizationDisabled) return;
                    this.fieldDefsArray.push({
                        name: field,
                        isCustom: defs.isCustom || false,
                        type: defs.type
                    });
                }, this);


                this.wait(false);
            }.bind(this));

        },

    });

});
