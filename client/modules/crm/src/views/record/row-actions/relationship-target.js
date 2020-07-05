

Espo.define('crm:views/record/row-actions/relationship-target', 'views/record/row-actions/relationship-unlink-only', function (Dep) {

    return Dep.extend({

        getActionList: function () {
            var list = Dep.prototype.getActionList.call(this);

            if (this.options.acl.edit) {
                if (this.model.get('isOptedOut')) {
                    list.push({
                        action: 'cancelOptOut',
                        html: this.translate('Cancel Opt-Out', 'labels', 'TargetList'),
                        data: {
                            id: this.model.id
                        }
                    });
                } else {
                    list.push({
                        action: 'optOut',
                        html: this.translate('Opt-Out', 'labels', 'TargetList'),
                        data: {
                            id: this.model.id
                        }
                    });
                }
            }
            return list;
        }

    });

});
