

define('views/record/panel-actions', 'view', function (Dep) {

    return Dep.extend({

        template: 'record/panel-actions',

        data: function () {
            return {
                defs: this.options.defs,
                buttonList: this.getButtonList(),
                actionList: this.getActionList(),
                entityType: this.options.entityType,
                scope: this.options.scope
            };
        },

        setup: function () {
            this.buttonList = this.options.defs.buttonList || [];
            this.actionList = this.options.defs.actionList || [];
        },

        getButtonList: function () {
            var list = [];
            this.buttonList.forEach(function (item) {
                if (item.hidden) return;
                list.push(item);
            }, this);
            return list;
        },

        getActionList: function () {
            var list = [];
            this.actionList.forEach(function (item) {
                if (item.hidden) return;
                list.push(item);
            }, this);
            return list;
        },

    });
});
