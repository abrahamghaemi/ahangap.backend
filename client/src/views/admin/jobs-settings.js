

Espo.define('views/admin/jobs-settings', 'views/settings/record/edit', function (Dep) {

    return Dep.extend({

        layoutName: 'jobsSettings',

        dynamicLogicDefs: {
            fields: {
                jobPoolConcurrencyNumber: {
                    visible: {
                        conditionGroup: [
                            {
                                type: 'isTrue',
                                attribute: 'jobRunInParallel'
                            }
                        ]
                    }
                }
            }
        },

        setup: function () {
            Dep.prototype.setup.call(this);
        }

    });
});
