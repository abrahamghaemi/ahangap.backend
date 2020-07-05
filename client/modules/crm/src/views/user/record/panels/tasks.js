

Espo.define('crm:views/user/record/panels/tasks', 'crm:views/record/panels/tasks', function (Dep) {

    return Dep.extend({

        listLayout: {
            rows: [
                [
                    {
                        name: 'name',
                        link: true,
                    },
                    {
                        name: 'isOverdue'
                    }
                ],
                [
                    {name: 'status'},
                    {name: 'dateEnd'}
                ]
            ]
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.getMetadata().get(['entityDefs', 'Task', 'fields', 'assignedUsers'])) {
                var foreignLink = this.getMetadata().get(['entityDefs', 'Task', 'links', 'assignedUsers', 'foreign']);
                if (foreignLink) {
                    this.link = foreignLink;
                }
            }
        }

    });

});
