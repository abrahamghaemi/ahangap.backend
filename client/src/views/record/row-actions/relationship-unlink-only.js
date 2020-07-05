

Espo.define('views/record/row-actions/relationship-unlink-only', 'views/record/row-actions/relationship', function (Dep) {

    return Dep.extend({

        getActionList: function () {
            if (this.options.acl.edit && !this.options.unlinkDisabled) {
                return [
                    {
                        action: 'unlinkRelated',
                        label: 'Unlink',
                        data: {
                            id: this.model.id
                        }
                    }
                ];
            }
        }
    });
});
