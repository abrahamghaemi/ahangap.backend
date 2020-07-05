

define('crm:views/meeting/modals/acceptance-status', 'views/modal', function (Dep) {

    return Dep.extend({

        backdrop: true,

        templateContent: `
            {{#each viewObject.statusDataList}}
                <p>
                    <button class="action btn btn-{{style}} btn-x-wide" type="button" data-action="setStatus" data-status="{{name}}">{{label}}</button>
                </p>
            {{/each}}
        `,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.headerHtml = this.escapeString(this.translate(this.model.entityType, 'scopeNames'))  + ' &raquo ' +
                this.escapeString(this.model.get('name')) + ' &raquo ' + this.translate('Acceptance', 'labels', 'Meeting');

            var statusList = this.getMetadata().get(['entityDefs', this.model.entityType, 'fields', 'acceptanceStatus', 'options']) || [];

            this.statusDataList = [];
            statusList.forEach(function (item) {
                var o = {
                    name: item,
                    style: this.getMetadata().get(['entityDefs', this.model.entityType, 'fields', 'acceptanceStatus', 'style', item]) || 'default',
                    label: this.getLanguage().translateOption(item, 'acceptanceStatus', this.model.entityType)
                };

                this.statusDataList.push(o);
            }, this);
        },

        actionSetStatus: function (data) {
            this.trigger('set-status', data.status);
            this.close();
        }
    });
});
