

Espo.define('views/admin/layouts/rows', 'views/admin/layouts/base', function (Dep) {

    return Dep.extend({

        template: 'admin/layouts/rows',

        events: _.extend({
            'click a[data-action="editItem"]': function (e) {
                this.editRow($(e.target).closest('li').data('name'));
            },
        }, Dep.prototype.events),

        dataAttributeList: null,

        dataAttributesDefs: {},

        editable: false,

        data: function () {
            return {
                scope: this.scope,
                type: this.type,
                buttonList: this.buttonList,
                enabledFields: this.enabledFields,
                disabledFields: this.disabledFields,
                layout: this.rowLayout,
                dataAttributeList: this.dataAttributeList,
                dataAttributesDefs: this.dataAttributesDefs,
                editable: this.editable,
            };
        },

        setup: function () {
            this.itemsData = {};
            Dep.prototype.setup.call(this);

            this.on('update-item', function (name, attributes) {
                console.log(name, attributes);
                this.itemsData[name] = Espo.Utils.cloneDeep(attributes);
            }, this);
        },

        editRow: function (name) {
            var attributes = Espo.Utils.cloneDeep(this.itemsData[name] || {});
            attributes.name = name;
            this.openEditDialog(attributes)
        },

        afterRender: function () {
            $('#layout ul.enabled, #layout ul.disabled').sortable({
                connectWith: '#layout ul.connected'
            });
        },

        fetch: function () {
            var layout = [];
            $("#layout ul.enabled > li").each(function (i, el) {
                var o = {};

                var name = $(el).data('name');

                var attributes = this.itemsData[name] || {};
                attributes.name = name;

                this.dataAttributeList.forEach(function (attribute) {
                    var value = attributes[attribute] || null;
                    if (value) {
                        o[attribute] = value;
                    }
                }, this);

                layout.push(o);
            }.bind(this));


            return layout;
        },

        validate: function (layout) {
            if (layout.length == 0) {
                this.notify('Layout cannot be empty', 'error');
                return false;
            }
            return true;
        }
    });
});

